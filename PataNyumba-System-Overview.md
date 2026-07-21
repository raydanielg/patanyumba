<div align="center">

# PataNyumba

### Modern Digital Rental House Discovery Platform

**Find. Verify. Rent — the smart way.**

![Status](https://img.shields.io/badge/status-active-success)
![Platform](https://img.shields.io/badge/platform-Flutter%20%7C%20Web-blue)
![Backend](https://img.shields.io/badge/backend-Laravel-red)
![Market](https://img.shields.io/badge/market-Tanzania%20%26%20Africa-green)

</div>

---

## Table of Contents

1. [Overview](#overview)
2. [System Architecture](#system-architecture)
3. [System Purpose](#system-purpose)
4. [Target Users](#target-users)
5. [Landlord & Agent Registration (KYC)](#landlord--agent-registration-kyc)
6. [Core Features](#core-features)
7. [Business Model](#business-model)
8. [Security](#security)
9. [Benefits](#benefits)
10. [Technology Stack](#technology-stack)
11. [Vision](#vision)

---

## Overview

**PataNyumba** is a modern digital rental house discovery platform designed to simplify the process of finding and listing rental properties. The platform connects **tenants** with **verified landlords** through an easy-to-use mobile application, while giving **administrators** a powerful web-based management system.

> **What makes it different:** Unlike traditional Property Management Systems (PMS), PataNyumba does **not** manage rent collection, lease agreements, tenant administration, or property maintenance. Instead, it focuses purely on helping users **discover available rental houses** quickly, securely, and efficiently.

Landlords can advertise available rental properties, while tenants search for houses using filters such as **location**, **rental price**, **property type**, and **available amenities**.

To ensure privacy and maintain a sustainable business model, sensitive information — including **landlord contact details** and the **exact property location** — is only unlocked after a tenant **purchases access** or **subscribes to a premium plan**.

### Problems We Solve

| Challenge in the Rental Market | How PataNyumba Fixes It |
| :--- | :--- |
| Fake property listings | Landlord verification & listing approval |
| Unreliable agents | Direct, verified connections |
| Time-consuming house searches | Advanced filters & smart search |
| Limited access to verified information | Trusted, moderated marketplace |

---

## System Architecture

The PataNyumba platform consists of **two primary components**:

### 1. Mobile Application *(Flutter)*

The primary platform for both **tenants** and **landlords**. Through the app, users can:

- Register and create an account
- Log in securely
- Search available rental houses
- Filter properties by multiple criteria
- Save favorite houses
- Upload and manage property listings *(Landlords)*
- View payment history
- Purchase access to premium property details
- Receive notifications about new listings and account activities

### 2. Web Administration Panel

Designed **exclusively for system administrators**, providing complete control over platform operations:

| Admin Capability | Description |
| :--- | :--- |
| User Management | Manage all platform users |
| Landlord Verification | Approve and verify landlords |
| House Listing Approval | Review and approve listings |
| Property Moderation | Maintain listing quality |
| Payment Management | Track and manage transactions |
| Subscription Management | Manage premium plans |
| Advertisement Management | Handle sponsored content |
| Reports & Analytics | Platform insights |
| Complaint Handling | Resolve user issues |
| System Configuration | Platform-wide settings |

---

## System Purpose

PataNyumba is built to provide a **centralized and trusted marketplace** where landlords can advertise rental properties while prospective tenants easily discover available houses based on their preferences.

The platform aims to:

- Eliminate unnecessary middlemen
- Reduce fraud
- Improve transparency in the rental housing market

---

## Target Users

| User Type | Role on the Platform |
| :--- | :--- |
| **Tenants** | Search and discover rental houses |
| **Individual Landlords** | List and advertise their own properties |
| **Property Owners** | Manage multiple rental properties |
| **Real Estate Agents** *(optional)* | List properties on behalf of owners |
| **System Administrators** | Oversee and moderate the platform |

---

## Landlord & Agent Registration (KYC)

To protect tenants from fraud and guarantee that every listing comes from a genuine source, **all landlords and agents must complete a KYC (Know Your Customer) verification** before their properties can go live on PataNyumba.

### Why KYC Matters

- **Eliminates fake listings** by confirming the real identity behind every account.
- **Builds tenant trust**, since only verified providers appear in search results.
- **Reduces disputes and scams**, protecting both tenants and legitimate landlords.
- **Ensures accountability**, linking every listing to a verified, traceable identity.

### KYC Onboarding Flow

```
Register  →  Submit Documents  →  Admin Review  →  Verified Badge  →  Publish Listings
```

| Step | Action | Responsible |
| :--- | :--- | :--- |
| 1 | Create account and verify phone via OTP | Landlord / Agent |
| 2 | Complete profile and select account type | Landlord / Agent |
| 3 | Upload required KYC documents | Landlord / Agent |
| 4 | Automated checks + manual review | System + Admin |
| 5 | Approval and issuance of **Verified** badge | Admin |
| 6 | Account unlocked to publish listings | System |

### Required KYC Information

**For Individual Landlords**

- Full legal name (matching official ID)
- National ID (NIDA), Passport, or Voter's ID
- Registered phone number (OTP-verified)
- Email address
- Live selfie / face photo for identity matching
- Residential or business address
- Proof of property ownership *(title deed, sale agreement, or utility bill)*

**For Agents / Agencies**

- Full legal name and business/company name
- National ID or Passport of the responsible person
- Business registration certificate (BRELA) or license
- TIN (Taxpayer Identification Number)
- Registered office address
- Live selfie / face photo for identity matching
- Authorization letter or mandate to list properties on behalf of owners

### Verification Levels

| Level | Requirements Met | What the Account Can Do |
| :--- | :--- | :--- |
| **Unverified** | Registered only | Browse the app; cannot publish listings |
| **Basic Verified** | Phone + ID confirmed | Publish a limited number of listings |
| **Fully Verified** | ID + ownership/business docs + face match | Unlimited listings, Verified badge, priority ranking |

### KYC Statuses

- **Pending** — documents submitted, awaiting admin review.
- **Approved** — verified successfully; listings can be published.
- **Rejected** — documents unclear or invalid; resubmission required.
- **Suspended** — verification revoked due to fraud or policy violation.

> **Note:** KYC documents are stored securely and encrypted. They are used strictly for verification and are never shared publicly or with tenants.

---

## Core Features

<table>
<tr>
<td valign="top">

**Accounts & Security**
- User Registration and Authentication
- Secure Login with OTP Verification
- Landlord & Agent KYC Verification
- User Profile Management

</td>
<td valign="top">

**Listings & Search**
- House Listing Management
- Property Search
- Advanced Search Filters
- Favorite Properties
- Property Comparison

</td>
</tr>
<tr>
<td valign="top">

**Media & Location**
- Property Image Gallery
- Video Property Tours
- Google Maps Integration
- House Availability Status

</td>
<td valign="top">

**Payments & Engagement**
- Property Unlock Payment
- Subscription Packages
- In-App & Push Notifications
- Reviews and Reporting
- Payment History

</td>
</tr>
</table>

---

## Business Model

PataNyumba generates revenue through:

| Revenue Stream | Description |
| :--- | :--- |
| **Property Unlock Fees** | Tenants pay to reveal contact details and exact location |
| **Premium Subscriptions** | Recurring plans for unlimited access |
| **Featured Advertisements** | Highlighted placement for properties |
| **Sponsored Listings** | Boosted visibility in search results |
| **Premium Landlord Accounts** | Enhanced tools and higher listing limits |

---

## Security

To maintain trust and platform integrity, PataNyumba incorporates several security features:

| Feature | Purpose |
| :--- | :--- |
| OTP Phone Verification | Confirm genuine users |
| Secure Authentication | Protect accounts |
| Verified Landlord & Agent Accounts (KYC) | Build trust and prevent fraud |
| Listing Approval Workflow | Prevent fake listings |
| Fraud Detection & Reporting | Flag suspicious activity |
| Encrypted User Information | Safeguard personal and KYC data |
| Secure Payment Verification | Protect transactions |
| Role-Based Access Control | Control permissions by user role |

---

## Benefits

### For Tenants
- Discover rental houses faster
- Search properties by location and budget
- Access verified property listings
- Save favorite houses
- Compare multiple properties
- Contact landlords securely after unlocking details

### For Landlords & Agents
- Advertise rental properties to a wide audience
- Manage property listings easily
- Increase property visibility
- Earn a Verified badge that boosts tenant confidence
- Connect with genuine prospective tenants
- Track listing performance

### For Administrators
- Centralized platform management
- User, landlord, and agent KYC verification
- Property moderation
- Payment monitoring
- Reports and analytics
- Platform configuration and maintenance

---

## Technology Stack

| Layer | Technology |
| :--- | :--- |
| **Mobile Application** | Flutter · Dart |
| **Backend** | Laravel REST API |
| **Database** | MySQL |
| **Web Administration** | Laravel · Blade / Vue.js |
| **Cloud Storage** | Cloudinary / Amazon S3 |
| **Maps & Location** | Google Maps API |
| **Notifications** | Firebase Cloud Messaging (FCM) |
| **Authentication** | JWT Authentication · OTP Verification |

### Payment Integration

<div align="center">

`M-Pesa` · `Airtel Money` · `Mixx by Yas` · `HaloPesa` · `T-Pesa` · `Visa` · `MasterCard`

</div>

---

## Vision

> PataNyumba aims to become the **leading rental house discovery platform in Tanzania and across Africa** by providing a secure, reliable, and technology-driven marketplace that connects tenants with verified landlords.
>
> Through **innovation, transparency, and user-friendly digital solutions**, the platform seeks to transform how people search for and access rental housing.

---

<div align="center">

**PataNyumba** — *Rethinking how Africa finds a home.*

</div>
