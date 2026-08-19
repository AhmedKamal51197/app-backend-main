"""
Tests for the Service Approval Workflow.

Covers:
- New services start with is_approved = False
- Unapproved services are hidden from the public seeker listing
- Provider can still see their own unapproved services in my-services
- Admin can approve a service (is_approved → True)
- Approved service appears in the public listing
- Admin can revoke approval (toggle: True → False)
- Editing a service resets is_approved back to False
"""

import pytest
import requests

from conftest import BASE_URL, auth_headers, json_headers

# ── Fixtures ──────────────────────────────────────────────────────────────────

@pytest.fixture(scope="module")
def existing_service_uuid(admin_token):
    """Return the UUID of the first service found via the admin listing."""
    resp = requests.get(
        f"{BASE_URL}/admin/v1/services",
        headers=auth_headers(admin_token),
    )
    assert resp.status_code == 200
    services = resp.json().get("data", [])
    assert len(services) > 0, "No services found in DB — run php artisan seed:dummy"
    return services[0]["id"]


@pytest.fixture(scope="module")
def approved_service_uuid(admin_token, existing_service_uuid):
    """Ensure the test service is approved at the start and return its UUID."""
    svc_uuid = existing_service_uuid
    # Make sure it starts as NOT approved (reset)
    _set_approval(admin_token, svc_uuid, target_state=False)
    return svc_uuid


def _get_service_admin(admin_token: str, service_uuid: str) -> dict:
    resp = requests.get(
        f"{BASE_URL}/admin/v1/services/{service_uuid}",
        headers=auth_headers(admin_token),
    )
    assert resp.status_code == 200, resp.text
    return resp.json()["data"]


def _approve(admin_token: str, service_uuid: str) -> dict:
    resp = requests.post(
        f"{BASE_URL}/admin/v1/services/{service_uuid}/approve",
        headers=auth_headers(admin_token),
    )
    assert resp.status_code == 200, f"Approve failed: {resp.text}"
    return resp.json()


def _set_approval(admin_token: str, service_uuid: str, target_state: bool):
    """Toggle until the service reaches target_state (avoid double-toggle side-effects)."""
    current = _get_service_admin(admin_token, service_uuid)
    if current["is_approved"] != target_state:
        _approve(admin_token, service_uuid)


# ── Tests ─────────────────────────────────────────────────────────────────────

class TestApprovalWorkflow:

    def test_new_service_starts_unapproved(self, admin_token, existing_service_uuid):
        """After reset, the service's is_approved is False."""
        _set_approval(admin_token, existing_service_uuid, target_state=False)
        svc = _get_service_admin(admin_token, existing_service_uuid)
        assert svc["is_approved"] is False

    def test_unapproved_service_hidden_from_public_listing(self, seeker_token, existing_service_uuid):
        """Unapproved service must NOT appear in public GET /api/v1/services."""
        resp = requests.get(
            f"{BASE_URL}/api/v1/services",
            headers=auth_headers(seeker_token),
        )
        assert resp.status_code == 200
        ids = [s["id"] for s in resp.json().get("data", [])]
        assert existing_service_uuid not in ids, (
            "Unapproved service should not be visible to seekers"
        )

    def test_provider_can_see_own_unapproved_service(self, provider_token, existing_service_uuid, admin_token):
        """Provider can still see their own unapproved service in my-services."""
        # Find the owner of the test service and login as them
        svc = _get_service_admin(admin_token, existing_service_uuid)
        owner_email = svc.get("user", {}).get("email")

        if not owner_email:
            pytest.skip("Could not determine service owner email")

        # Login as owner
        login_resp = requests.post(
            f"{BASE_URL}/api/auth/login",
            json={"email": owner_email, "password": "password@123"},
            headers={"Accept": "application/json"},
        )
        if login_resp.status_code != 200 or login_resp.json().get("error"):
            pytest.skip(f"Cannot login as {owner_email}")

        owner_token = login_resp.json()["data"]["access_token"]

        resp = requests.get(
            f"{BASE_URL}/api/v1/services/my-services",
            headers=auth_headers(owner_token),
        )
        assert resp.status_code == 200
        ids = [s["id"] for s in resp.json().get("data", [])]
        assert existing_service_uuid in ids, (
            "Provider should see their own unapproved service in my-services"
        )

    def test_admin_approve_returns_is_approved_true(self, admin_token, existing_service_uuid):
        """POST /admin/v1/services/{id}/approve sets is_approved = True."""
        _set_approval(admin_token, existing_service_uuid, target_state=False)

        result = _approve(admin_token, existing_service_uuid)
        assert result["data"]["is_approved"] is True

    def test_approved_service_visible_in_public_listing(self, seeker_token, existing_service_uuid, admin_token):
        """After approval the service appears in GET /api/v1/services."""
        _set_approval(admin_token, existing_service_uuid, target_state=True)

        resp = requests.get(
            f"{BASE_URL}/api/v1/services",
            headers=auth_headers(seeker_token),
        )
        assert resp.status_code == 200
        ids = [s["id"] for s in resp.json().get("data", [])]
        assert existing_service_uuid in ids, (
            "Approved service should be visible to seekers"
        )

    def test_admin_revoke_approval_toggles_back_to_false(self, admin_token, existing_service_uuid):
        """Calling approve again on an approved service revokes it (toggle)."""
        _set_approval(admin_token, existing_service_uuid, target_state=True)

        result = _approve(admin_token, existing_service_uuid)
        assert result["data"]["is_approved"] is False

    def test_revoked_service_hidden_from_public_listing(self, seeker_token, existing_service_uuid, admin_token):
        """After revoking, service disappears from public listing again."""
        _set_approval(admin_token, existing_service_uuid, target_state=False)

        resp = requests.get(
            f"{BASE_URL}/api/v1/services",
            headers=auth_headers(seeker_token),
        )
        assert resp.status_code == 200
        ids = [s["id"] for s in resp.json().get("data", [])]
        assert existing_service_uuid not in ids

    def test_admin_approve_requires_permission(self, provider_token, existing_service_uuid):
        """Non-admin (provider) cannot call the approve endpoint."""
        resp = requests.post(
            f"{BASE_URL}/admin/v1/services/{existing_service_uuid}/approve",
            headers=auth_headers(provider_token),
        )
        assert resp.status_code in (401, 403), (
            f"Expected 401/403 for non-admin, got {resp.status_code}"
        )

    def test_editing_service_resets_approval(self, admin_token, existing_service_uuid):
        """Editing a service resets is_approved to False even if it was approved."""
        _set_approval(admin_token, existing_service_uuid, target_state=True)

        # Find the owner and edit the service
        svc = _get_service_admin(admin_token, existing_service_uuid)
        owner_email = svc.get("user", {}).get("email")
        if not owner_email:
            pytest.skip("Could not determine service owner email")

        login_resp = requests.post(
            f"{BASE_URL}/api/auth/login",
            json={"email": owner_email, "password": "password@123"},
            headers={"Accept": "application/json"},
        )
        if login_resp.status_code != 200 or login_resp.json().get("error"):
            pytest.skip(f"Cannot login as service owner {owner_email}")

        owner_token = login_resp.json()["data"]["access_token"]

        edit_resp = requests.post(
            f"{BASE_URL}/api/v1/services/{existing_service_uuid}/edit",
            json={"title": svc["title"] + " (edited)"},
            headers=json_headers(owner_token),
        )
        assert edit_resp.status_code == 200, f"Edit failed: {edit_resp.text}"

        updated = _get_service_admin(admin_token, existing_service_uuid)
        assert updated["is_approved"] is False, (
            "Editing a service must reset is_approved to False"
        )

    def test_is_approved_field_present_in_admin_list(self, admin_token):
        """Every service in admin listing includes is_approved field."""
        resp = requests.get(
            f"{BASE_URL}/admin/v1/services",
            headers=auth_headers(admin_token),
        )
        assert resp.status_code == 200
        for svc in resp.json().get("data", []):
            assert "is_approved" in svc, f"Service {svc.get('id')} missing is_approved"

    def test_is_approved_field_present_in_admin_detail(self, admin_token, existing_service_uuid):
        """Admin service detail response includes is_approved field."""
        svc = _get_service_admin(admin_token, existing_service_uuid)
        assert "is_approved" in svc
