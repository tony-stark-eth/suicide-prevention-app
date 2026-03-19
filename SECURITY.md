# Security Policy

## Reporting a vulnerability

**Please do not open a public GitHub issue for security vulnerabilities.**

This is especially important for this project. A public disclosure could:
- Expose a way to bypass the `SafetyOutputFilter`, putting vulnerable users at risk
- Reveal information that could be misused by bad actors

### How to report

Email: **security@yourdomain.help** *(update this before going public)*

Include:
- A description of the vulnerability
- Steps to reproduce
- The potential impact
- Your suggested fix (optional but appreciated)

You will receive an acknowledgement within 48 hours and a resolution timeline within 7 days.

We will credit you in the release notes unless you prefer to remain anonymous.

## Scope

| In scope | Out of scope |
|---|---|
| `SafetyOutputFilter` bypass | Vulnerabilities in vendor dependencies (report upstream) |
| Rate limiter bypass on AI endpoints | Social engineering |
| User data leakage (there should be none) | Denial of service via resource exhaustion |
| Encryption weaknesses in `FollowupService` | Issues in test/dev environments only |
| XSS or injection in any rendered output | |
| DSGVO/privacy violations | |

## Supported versions

Only the latest commit on `main` is actively maintained.

## Disclosure policy

We follow coordinated disclosure. Once a fix is released, we will publish a summary of the vulnerability and the fix in the GitHub release notes. We will not publicly disclose details before a fix is available.
