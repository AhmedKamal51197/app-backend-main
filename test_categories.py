import requests
import json
import argparse

def main():
    parser = argparse.ArgumentParser(description="Test User Category Endpoints")
    parser.add_argument("--url", default="http://localhost:8000/api/v1", help="Base URL of the API")
    parser.add_argument("--token", required=True, help="Bearer token for authentication")
    parser.add_argument("--category", required=True, type=str, help="Category UUID to set")
    parser.add_argument("--subcategory", required=True, type=str, help="SubCategory UUID to set")
    parser.add_argument("--provider-uuid", required=True, help="UUID of a provider to test provider profile")
    
    args = parser.parse_args()
    
    headers = {
        "Authorization": f"Bearer {args.token}",
        "Accept": "application/json",
        "Content-Type": "application/json"
    }

    # 1. Edit Category
    print("--------------------------------------------------")
    print("1. Testing POST /user/edit/category")
    payload = {
        "category_id": args.category,
        "sub_category_id": args.subcategory
    }
    
    response = requests.post(f"{args.url}/user/edit/category", headers=headers, json=payload)
    print(f"Status Code: {response.status_code}")
    try:
        data = response.json()
        print("Response:", json.dumps(data, indent=2))
        
        if "data" in data and "category" in data["data"] and "sub_category" in data["data"]:
            print("=> SUCCESS: category and sub_category are in the response.")
        else:
            print("=> FAILED: missing fields in response.")
    except Exception as e:
        print("Error parsing JSON:", e)

    # 2. Get User Profile
    print("--------------------------------------------------")
    print("2. Testing GET /user/profile")
    response = requests.get(f"{args.url}/user/profile", headers=headers)
    print(f"Status Code: {response.status_code}")
    try:
        data = response.json()
        if "data" in data and "category" in data["data"] and "sub_category" in data["data"]:
            print("=> SUCCESS: category and sub_category are present in User Profile.")
        else:
            print("=> FAILED: missing fields in user profile.")
    except Exception as e:
        print("Error parsing JSON:", e)
        
    # 3. Get Provider Profile
    print("--------------------------------------------------")
    print(f"3. Testing GET /providers/{args.provider_uuid}")
    response = requests.get(f"{args.url}/providers/{args.provider_uuid}", headers=headers)
    print(f"Status Code: {response.status_code}")
    try:
        data = response.json()
        if "data" in data and "category" in data["data"] and "sub_category" in data["data"]:
            print("=> SUCCESS: category and sub_category are present in Provider Profile.")
        else:
            print("=> FAILED: missing fields in provider profile.")
    except Exception as e:
        print("Error parsing JSON:", e)

if __name__ == "__main__":
    main()
