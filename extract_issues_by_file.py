import json
from collections import defaultdict

with open('sonarqube_issues.json', 'r') as f:
    data = json.load(f)

# Group by file and rule
by_file = defaultdict(lambda: defaultdict(list))

for issue in data:
    component = issue.get('component', '')
    file_path = component.split(':')[-1] if ':' in component else component
    rule = issue.get('rule', 'unknown')
    severity = issue.get('severity', 'UNKNOWN')
    
    by_file[file_path][rule].append({
        'line': issue.get('line'),
        'message': issue.get('message'),
        'severity': severity
    })

# Write summary
with open('sonarqube_issues_by_file.txt', 'w') as f:
    for file_path in sorted(by_file.keys()):
        f.write(f"\n{'='*80}\n{file_path}\n{'='*80}\n")
        for rule in sorted(by_file[file_path].keys()):
            issues = by_file[file_path][rule]
            f.write(f"\n{rule} ({len(issues)} issues):\n")
            for issue in issues[:5]:  # First 5 of each rule
                f.write(f"  Line {issue['line']} [{issue['severity']}]: {issue['message'][:100]}\n")
            if len(issues) > 5:
                f.write(f"  ... and {len(issues) - 5} more\n")

print("Issues extracted to sonarqube_issues_by_file.txt")

