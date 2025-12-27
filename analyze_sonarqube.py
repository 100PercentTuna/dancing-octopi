import json
from collections import defaultdict

# Read the JSON file
with open('sonarqube_issues.json', 'r') as f:
    data = json.load(f)

# Categorize issues
rules = defaultdict(int)
severity_by_rule = defaultdict(lambda: defaultdict(int))
files = defaultdict(list)
critical_issues = []
major_issues = []

for issue in data:
    rule = issue.get('rule', 'unknown')
    severity = issue.get('severity', 'UNKNOWN')
    component = issue.get('component', '')
    file_path = component.split(':')[-1] if ':' in component else component
    
    rules[rule] += 1
    severity_by_rule[rule][severity] += 1
    files[file_path].append(issue)
    
    if severity == 'CRITICAL':
        critical_issues.append(issue)
    elif severity == 'MAJOR':
        major_issues.append(issue)

# Print summary
print(f"Total issues: {len(data)}")
print(f"CRITICAL: {len(critical_issues)}")
print(f"MAJOR: {len(major_issues)}")
print(f"MINOR: {len(data) - len(critical_issues) - len(major_issues)}")

print("\n=== Top 20 Rules ===")
for rule, count in sorted(rules.items(), key=lambda x: -x[1])[:20]:
    print(f"{rule}: {count}")

print("\n=== CRITICAL Issues (first 20) ===")
for issue in critical_issues[:20]:
    component = issue.get('component', '')
    file_path = component.split(':')[-1] if ':' in component else component
    print(f"{issue.get('rule')} - {file_path}:{issue.get('line')} - {issue.get('message', '')[:100]}")

print("\n=== MAJOR Issues (first 20) ===")
for issue in major_issues[:20]:
    component = issue.get('component', '')
    file_path = component.split(':')[-1] if ':' in component else component
    print(f"{issue.get('rule')} - {file_path}:{issue.get('line')} - {issue.get('message', '')[:100]}")

# Group by file
print("\n=== Issues by File ===")
for file_path, issues in sorted(files.items(), key=lambda x: -len(x[1]))[:10]:
    print(f"{file_path}: {len(issues)} issues")

