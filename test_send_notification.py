import requests
import json
import argparse

def main():
    parser = argparse.ArgumentParser(description="Test Send Custom Notification Endpoint")
    parser.add_argument("--url", default="http://localhost:8000/api/v1", help="Base URL of the API")
    parser.add_argument("--token", required=True, help="Bearer token for authentication")
    parser.add_argument("--title", required=True, help="Notification Title")
    parser.add_argument("--body", required=True, help="Notification Body")
    
    args = parser.parse_args()
    
    headers = {
        "Authorization": f"Bearer {args.token}",
        "Accept": "application/json",
        "Content-Type": "application/json"
    }

    print("--------------------------------------------------")
    print(f"Testing POST {args.url}/user/send-notification")
    payload = {
        "title": args.title,
        "body": args.body
    }
    
    response = requests.post(f"{args.url}/user/send-notification", headers=headers, json=payload)
    print(f"Status Code: {response.status_code}")
    try:
        data = response.json()
        print("Response:", json.dumps(data, indent=2))
        
        if response.status_code == 200:
            print("=> SUCCESS: Notification sent!")
        else:
            print("=> FAILED: Unexpected status code.")
    except Exception as e:
        print("Error parsing JSON:", e)
        print("Response text:", response.text)

if __name__ == "__main__":
    main()
