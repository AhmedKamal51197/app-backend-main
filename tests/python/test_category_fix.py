"""
Tests for the Category Bug Fix.

Covers:
- ServiceResource exposes category_id and sub_category_id UUIDs at root level
- Editing a service with category_id updates the category correctly
- Editing a service with sub_category_id updates the sub-category correctly
- category_id and sub_category_id are validated (non-existent UUID returns 422)
- Returned category_id UUID matches the category object's id
"""

import pytest
import requests

from conftest import BASE_URL, auth_headers, json_headers


# ── Helpers ───────────────────────────────────────────────────────────────────

def _get_service_admin(admin_token: str, service_uuid: str) -> dict:
    resp = requests.get(
        f"{BASE_URL}/admin/v1/services/{service_uuid}",
        headers=auth_headers(admin_token),
    )
    assert resp.status_code == 200, resp.text
    return resp.json()["data"]


def _get_service_as_owner(owner_token: str, service_uuid: str) -> dict:
    resp = requests.get(
        f"{BASE_URL}/api/v1/services/{service_uuid}",
        headers=auth_headers(owner_token),
    )
    assert resp.status_code == 200, resp.text
    return resp.json()["data"]


def _owner_token_for_service(admin_token: str, service_uuid: str):
    svc = _get_service_admin(admin_token, service_uuid)
    email = svc.get("user", {}).get("email")
    if not email:
        return None
    login = requests.post(
        f"{BASE_URL}/api/auth/login",
        json={"email": email, "password": "password@123"},
        headers={"Accept": "application/json"},
    )
    if login.status_code != 200 or login.json().get("error"):
        return None
    return login.json()["data"]["access_token"]


# ── Fixtures ──────────────────────────────────────────────────────────────────

@pytest.fixture(scope="module")
def service_uuid(admin_token):
    resp = requests.get(
        f"{BASE_URL}/admin/v1/services",
        headers=auth_headers(admin_token),
    )
    assert resp.status_code == 200
    services = resp.json().get("data", [])
    assert len(services) > 0, "No services in DB"
    return services[0]["id"]


@pytest.fixture(scope="module")
def category_uuids(admin_token):
    """Return (category_uuid, sub_category_uuid) from the first service."""
    resp = requests.get(
        f"{BASE_URL}/admin/v1/services",
        headers=auth_headers(admin_token),
    )
    data = resp.json().get("data", [])
    svc = data[0]
    return svc["category"]["id"], svc["sub_category"]["id"]


# ── Tests ─────────────────────────────────────────────────────────────────────

class TestCategoryFix:

    def test_service_response_exposes_category_id_uuid(self, admin_token, service_uuid):
        """ServiceResource must include category_id at the root level."""
        svc = _get_service_admin(admin_token, service_uuid)
        assert "category_id" in svc, "category_id UUID missing from service response"
        assert svc["category_id"] is not None

    def test_service_response_exposes_sub_category_id_uuid(self, admin_token, service_uuid):
        """ServiceResource must include sub_category_id at the root level."""
        svc = _get_service_admin(admin_token, service_uuid)
        assert "sub_category_id" in svc, "sub_category_id UUID missing from service response"
        assert svc["sub_category_id"] is not None

    def test_category_id_matches_nested_category_id(self, admin_token, service_uuid):
        """Root-level category_id must match the id inside the nested category object."""
        svc = _get_service_admin(admin_token, service_uuid)
        assert svc["category_id"] == svc["category"]["id"]

    def test_sub_category_id_matches_nested_sub_category_id(self, admin_token, service_uuid):
        """Root-level sub_category_id must match the id inside the nested sub_category object."""
        svc = _get_service_admin(admin_token, service_uuid)
        assert svc["sub_category_id"] == svc["sub_category"]["id"]

    def test_edit_service_updates_category(self, admin_token, service_uuid, category_uuids):
        """Sending category_id in edit request updates the service's category."""
        owner_token = _owner_token_for_service(admin_token, service_uuid)
        if not owner_token:
            pytest.skip("Cannot get owner token")

        cat_uuid, sub_uuid = category_uuids

        resp = requests.post(
            f"{BASE_URL}/api/v1/services/{service_uuid}/edit",
            json={"category_id": cat_uuid, "sub_category_id": sub_uuid},
            headers=json_headers(owner_token),
        )
        assert resp.status_code == 200, f"Edit failed: {resp.text}"

        updated = resp.json()["data"]
        assert updated["category_id"] == cat_uuid, (
            f"category_id mismatch: expected {cat_uuid}, got {updated.get('category_id')}"
        )
        assert updated["sub_category_id"] == sub_uuid, (
            f"sub_category_id mismatch: expected {sub_uuid}, got {updated.get('sub_category_id')}"
        )

    def test_edit_service_with_invalid_category_uuid_returns_422(self, admin_token, service_uuid):
        """Sending a non-existent category UUID must return 422 validation error."""
        owner_token = _owner_token_for_service(admin_token, service_uuid)
        if not owner_token:
            pytest.skip("Cannot get owner token")

        resp = requests.post(
            f"{BASE_URL}/api/v1/services/{service_uuid}/edit",
            json={"category_id": "00000000-0000-0000-0000-000000000000"},
            headers=json_headers(owner_token),
        )
        assert resp.status_code == 422, (
            f"Expected 422 for invalid category UUID, got {resp.status_code}: {resp.text}"
        )

    def test_edit_service_without_category_preserves_existing(self, admin_token, service_uuid):
        """Editing only title must not change the existing category."""
        before = _get_service_admin(admin_token, service_uuid)
        original_cat_id = before["category_id"]

        owner_token = _owner_token_for_service(admin_token, service_uuid)
        if not owner_token:
            pytest.skip("Cannot get owner token")

        resp = requests.post(
            f"{BASE_URL}/api/v1/services/{service_uuid}/edit",
            json={"title": before["title"]},
            headers=json_headers(owner_token),
        )
        assert resp.status_code == 200

        after = resp.json()["data"]
        assert after["category_id"] == original_cat_id, (
            "category_id should not change when not included in edit payload"
        )

    def test_my_services_response_includes_category_id(self, admin_token, service_uuid):
        """Provider's my-services list also exposes category_id and sub_category_id."""
        owner_token = _owner_token_for_service(admin_token, service_uuid)
        if not owner_token:
            pytest.skip("Cannot get owner token")

        resp = requests.get(
            f"{BASE_URL}/api/v1/services/my-services",
            headers=auth_headers(owner_token),
        )
        assert resp.status_code == 200
        services = resp.json().get("data", [])
        assert len(services) > 0

        for svc in services:
            assert "category_id" in svc, f"Service {svc['id']} missing category_id"
            assert "sub_category_id" in svc, f"Service {svc['id']} missing sub_category_id"
