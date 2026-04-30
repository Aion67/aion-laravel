# Aion Pharmacy
Aion Pharmacy is a web application built in laravel framework used for pharmacy management. It provides a user-friendly interface for managing inventory, sales, and customer information. The application is designed to streamline the operations of a pharmacy and improve efficiency. With features such as real-time inventory tracking, sales reporting, and customer management, Aion Pharmacy is an essential tool for any pharmacy looking to improve their operations.
It's designred primarily for small to medium-sized retail pharmacies.

### Sample user story
As a pharmacy manager/admin, I want to be able to keep traok of inventory, stock levels, and sales so that I can make informed decisions about purchasing and staffing. With Aion Pharmacy, I can easily view real-time inventory levels, track sales trends, and manage customer information all in one place. This allows me to optimize my operations and provide better service to my customers.
As a pharmacist, I want to be able to quickly and easily create prescriptions and confirm purchases and their details, manage patient information, and track medication history.

## Typical scenario
1. A customer walks into the pharmacy and approaches the counter.
2. If customer is registered, proceed otherwise, register the customer by collecting their information(DOB, name, contact,sex, optional[medical history, allergies and conditions])
3. The pharmacist uses the information from user to create a prescription for the customer by selecting available medications, their quantity, and dosage.
4. The pharmacist confirms the purchase and provides the customer with a receipts
5. Inventory is updated in real-time, sale is tracked, and customer leaves.

## Key actors
- Admin: Responsible for managing the overall operations of the pharmacy, including inventory management, sales tracking, and customer management.
- Pharmacist: Responsible for creating prescriptions, confirming purchases, and managing patient information.
- Customer: The end-user of the pharmacy who purchases medications and receives services. Does not have access to the system but interacts with the pharmacist and admin for their needs.

## Tech stack
- Laravel: A PHP framework used for building the web application.
- MySQL: A relational database management system used for storing data.
- Blade: A templating engine used for creating the user interface.
- Docker: A containerization platform used for deploying the application in a consistent environment.
