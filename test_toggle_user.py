import requests
import json
import argparse

def main():
    parser = argparse.ArgumentParser(description="Test Toggle User Activation Endpoint")
    parser.add_argument("--url", default="http://localhost:8000/api/v1", help="Base URL of the API")
    parser.add_argument("--token", required=True, help="Bearer token for admin authentication")
    parser.add_argument("--user-uuid", required=True, help="UUID of a user to toggle")
    
    args = parser.parse_args()
    
    headers = {
        "Authorization": f"Bearer {args.token}",
        "Accept": "application/json",
        "Content-Type": "application/json"
    }

    print("--------------------------------------------------")
    print(f"Testing POST {args.url}/admin/users/{args.user_uuid}/toggle-active")
    
    response = requests.post(f"{args.url}/admin/users/{args.user_uuid}/toggle-active", headers=headers)
    print(f"Status Code: {response.status_code}")
    
    try:
        data = response.json()
        
        if response.status_code == 200 and "data" in data:
            print("=> SUCCESS: User activation toggled!")
            user_data = data["data"]
            print(f"   New 'active' status: {user_data.get('active')}")
        else:
            print("=> FAILED: Unexpected status code or missing data.")
            print("Response:", json.dumps(data, indent=2))
            
    except Exception as e:
        print("Error parsing JSON:", e)
        print("Response text:", response.text)

if __name__ == "__main__":
    main()
