import pandas as pd
import json

# Read the Excel file
df = pd.read_excel('reference-files/sonarqube_issues_latest_month.xlsx')

# Print summary
print(f"Total issues: {len(df)}")
print(f"\nColumns: {list(df.columns)}")

# Print severity and type counts
if 'severity' in df.columns:
    print(f"\nSeverity counts:\n{df['severity'].value_counts()}")
if 'type' in df.columns:
    print(f"\nType counts:\n{df['type'].value_counts()}")

# Export to JSON for easier processing
df.to_json('sonarqube_issues.json', orient='records', indent=2)
print(f"\nExported to sonarqube_issues.json")

# Print first few issues
print("\n\nFirst 10 issues:")
print(df.head(10).to_string())

