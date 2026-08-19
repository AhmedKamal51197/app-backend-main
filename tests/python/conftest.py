import pytest
import requests

BASE_URL = "http://127.0.0.1:9090"

ADMIN_EMAIL    = "info@moawen.sa"
ADMIN_PASSWORD = "Info@123"

PROVIDER_EMAIL    = "dudley83@example.net"
PROVIDER_PASSWORD = "password@123"

SEEKER_EMAIL    = "ohara.hoyt@example.com"
SEEKER_PASSWORD = "password@123"


def _login(email: str, password: str) -> str:
    resp = requests.post(
        f"{BASE_URL}/api/auth/login",
        json={"email": email, "password": password},
        headers={"Accept": "application/json"},
    )
    assert resp.status_code == 200, f"Login failed for {email}: {resp.text}"
    data = resp.json()
    assert not data.get("error"), f"Login error for {email}: {data.get('message')}"
    return data["data"]["access_token"]


@pytest.fixture(scope="session")
def admin_token():
    return _login(ADMIN_EMAIL, ADMIN_PASSWORD)


@pytest.fixture(scope="session")
def provider_token():
    return _login(PROVIDER_EMAIL, PROVIDER_PASSWORD)


@pytest.fixture(scope="session")
def seeker_token():
    return _login(SEEKER_EMAIL, SEEKER_PASSWORD)


def auth_headers(token: str) -> dict:
    return {"Authorization": f"Bearer {token}", "Accept": "application/json"}


def json_headers(token: str) -> dict:
    return {**auth_headers(token), "Content-Type": "application/json"}
