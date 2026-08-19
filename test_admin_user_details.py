import requests
import json
import argparse

def main():
    parser = argparse.ArgumentParser(description="Test Admin User Details Endpoint")
    parser.add_argument("--url", default="http://localhost:8000/api/v1", help="Base URL of the API")
    parser.add_argument("--token", required=True, help="Bearer token for admin authentication")
    parser.add_argument("--user-uuid", required=True, help="UUID of a user to fetch details for")
    
    args = parser.parse_args()
    
    headers = {
        "Authorization": f"Bearer {args.token}",
        "Accept": "application/json",
        "Content-Type": "application/json"
    }

    print("--------------------------------------------------")
    print(f"Testing GET {args.url}/admin/users/{args.user_uuid}")
    
    response = requests.get(f"{args.url}/admin/users/{args.user_uuid}", headers=headers)
    print(f"Status Code: {response.status_code}")
    
    try:
        data = response.json()
        
        if response.status_code == 200 and "data" in data:
            print("=> SUCCESS: Fetched admin user details!")
            user_data = data["data"]
            
            if "open_tickets_count" in user_data:
                print(f"   open_tickets_count is present (Value: {user_data['open_tickets_count']})")
            else:
                print("   [!] open_tickets_count is missing from response")
                
            if "reports" in user_data:
                print(f"   reports list is present (Count: {len(user_data['reports'])})")
            else:
                print("   [!] reports is missing from response")
                
            if "reports_count" in user_data:
                print(f"   reports_count is present (Value: {user_data['reports_count']})")
            else:
                print("   [!] reports_count is missing from response")
                
        else:
            print("=> FAILED: Unexpected status code or missing data.")
            print("Response:", json.dumps(data, indent=2))
            
    except Exception as e:
        print("Error parsing JSON:", e)
        print("Response text:", response.text)

if __name__ == "__main__":
    main()
