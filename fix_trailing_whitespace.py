import os
import re

# Get all trailing whitespace issues from JSON
import json
with open('sonarqube_issues.json', 'r') as f:
    data = json.load(f)

trailing_ws = [i for i in data if i.get('rule') == 'php:S1131']

# Group by file
by_file = {}
for issue in trailing_ws:
    component = issue.get('component', '')
    file_path = component.split(':')[-1] if ':' in component else component
    line_num = issue.get('line')
    
    if file_path not in by_file:
        by_file[file_path] = []
    by_file[file_path].append(line_num)

# Fix each file
fixed_count = 0
for file_path, lines in by_file.items():
    if not os.path.exists(file_path):
        continue
    
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        lines_content = content.split('\n')
        original_lines = len(lines_content)
        
        # Remove trailing whitespace from specified lines
        for line_num in lines:
            idx = line_num - 1  # Convert to 0-based index
            if 0 <= idx < len(lines_content):
                lines_content[idx] = lines_content[idx].rstrip()
        
        new_content = '\n'.join(lines_content)
        
        if new_content != content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(new_content)
            fixed_count += len(lines)
            print(f"Fixed {len(lines)} lines in {file_path}")
    except Exception as e:
        print(f"Error fixing {file_path}: {e}")

print(f"\nTotal: Fixed trailing whitespace in {fixed_count} lines across {len(by_file)} files")

