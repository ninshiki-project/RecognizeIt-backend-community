# Proposal Template



---

**Proposal for Launching the Ninshiki App**

**Objective:**  
The Ninshiki app is an internal company tool designed to enhance employee engagement and recognition by enabling employees to earn points when acknowledged for their contributions through posts. These points can be redeemed for items in a dedicated shop. The app aims to foster a culture of appreciation, improve collaboration, and boost overall productivity.

**Disclosure:**  
The initial codebase for the Ninshiki app is hosted in a public GitHub repository: [https://github.com/ninshiki-project/Ninshiki-backend](https://github.com/ninshiki-project/Ninshiki-backend). It will remain open-source under the **GPL-3.0 License**.

This proposal outlines the development and deployment of a customized version of the Ninshiki app for internal company use. While the company will have the right to use, modify, and distribute this internal version for its own purposes, the ownership and rights to the original open-source codebase will remain with the public community under its open-source license. The company will not acquire any proprietary rights over the codebase or any subsequent developments made within the open-source ecosystem.

**Key Features of Ninshiki:**
- **Points and Recognition System:** Employees earn points when recognized by their peers through posts.
- **Redemption System:** Employees can redeem points for items from the shop, with all requests manually reviewed by administrators.

**Technical Requirements:**

1. **Server Infrastructure**  
   We propose three hosting options on DigitalOcean, tailored to the company’s needs:

    - **Option 1: Minimum Requirement**
        - Droplet Type: Basic Droplet with standard CPUs
        - Specifications:
            - CPU: 1 vCPU
            - Memory: 2 GB RAM
            - Storage: 40 GB SSD
            - Bandwidth: 1 TB/month
            - Region: Closest to company headquarters or primary user base to minimize latency
            - Operating System: Ubuntu 22.04 LTS

    - **Option 2: Intermediate Requirement**
        - Droplet Type: Basic Droplet with standard CPUs
        - Specifications:
            - CPU: 2 vCPUs
            - Memory: 4 GB RAM
            - Storage: 80 GB SSD
            - Bandwidth: 2 TB/month
            - Region: Closest to company headquarters or primary user base to minimize latency
            - Operating System: Ubuntu 22.04 LTS

    - **Option 3: Recommended Requirement**
        - Droplet Type: Basic Droplet with premium Intel or AMD CPUs
        - Specifications:
            - CPU: 4 vCPUs
            - Memory: 8 GB RAM
            - Storage: 160 GB SSD (scalable as needed)
            - Bandwidth: 4 TB/month
            - Region: Closest to company headquarters or primary user base to minimize latency
            - Operating System: Ubuntu 22.04 LTS

    - **Add-ons:**
        - Managed Database: MySQL for secure and scalable data storage

2. **Application Framework and Tools**
    - Backend: Laravel (PHP) (licensed under the MIT License)
    - Frontend: Vue.js and Inertia.js (licensed under the MIT License)
    - Database: MySQL, hosted on DigitalOcean’s managed database service
    - Caching: Redis for session management and performance optimization

3. **Third-Party Integrations**
    - Media Management: Cloudinary for efficient image and media asset storage and delivery

4. **Security Requirements**
    - SSL/TLS for encrypted communication
    - Regular security updates and patches
    - Role-based access control (RBAC) to ensure proper data access permissions
    - Web Application Firewall (WAF) to protect against common threats

5. **Necessary Tools and Accounts**
    - **Cloudinary Account:** For image and media asset storage and delivery.
    - **Domain Provider Account (e.g., GoDaddy, Namecheap):** For acquiring and managing the domain name for the Ninshiki app (e.g., "app.yourcompany.com").
    - **DigitalOcean Account:** For hosting the application on their servers.
    - **GitHub Account:** For version control and collaborative development.
    - **Resend Account:** For sending email notifications.

**Deployment Plan:**

- **Development and Staging Environment:**
    - Create separate environments for development and testing using DigitalOcean droplets.
    - Implement CI/CD pipelines for seamless code deployment.

- **Initial Launch:**
    - Conduct a soft launch with a small group of employees for beta testing.
    - Gather feedback and resolve any bugs or usability issues.

- **Full Rollout:**
    - Gradually roll out the app to all employees.
    - Provide training sessions and comprehensive documentation to ensure smooth adoption.

**Estimated Costs:**

- **DigitalOcean Hosting:**
    - Minimum Requirement: ~\$6/month (refer to DigitalOcean Droplet pricing: [https://www.digitalocean.com/pricing/droplets/](https://www.digitalocean.com/pricing/droplets/))
    - Intermediate Requirement: ~\$12/month (refer to DigitalOcean Droplet pricing: [https://www.digitalocean.com/pricing/droplets/](https://www.digitalocean.com/pricing/droplets/))
    - Recommended Requirement: ~\$48/month (scalable as needed) (refer to DigitalOcean Droplet pricing: [https://www.digitalocean.com/pricing/droplets/](https://www.digitalocean.com/pricing/droplets/))

- **Third-Party Tools and APIs:**
    - Cloudinary: Free edition can be used initially for efficient image and media asset storage and delivery.

**Benefits to the Company:**
- Encourages a culture of recognition and collaboration.
- Boosts employee engagement and satisfaction.
- Provides actionable insights into employee interactions and contributions.

