##########################################
##      Frontend Side Developemnt       ##
##########################################

### Router Configuration (resources/js/router.js) ###

This file is responsible for defining the routes of the Vue.js application and managing authentication checks before allowing access to certain routes.

### Imports
=> createRouter and createWebHistory from Vue Router are imported to define and create the router.
### Components:
=> Login (./components/Login.vue): The login page component.
=> Register (./components/Register.vue): The registration page component.
=> Home (./components/Home.vue): The home page component.
=> StudentList (./components/student/StudentList.vue): A component displaying the list of students.
=> StudentAdd (./components/student/StudentAdd.vue): A component used to add new students.
=> axios is used for making API requests and setting the authorization header using a stored token (localStorage.getItem('authToken')).

### Authentication Check (checkAuth) ###

This function verifies if the user is authenticated by making an API request to /api/user using Axios:

### Logic: ###
=> If the user is authenticated (response.data exists), the router proceeds to the requested route (next()).
=> If the user is not authenticated and tries to access a protected route (anything other than /login), they are redirected to the login page.
=> In case of an error (e.g., the token is invalid or expired), the user is redirected to the login page if they attempt to access a restricted route.

### Routes ###
The following routes are defined in the application:

=> /login: Renders the Login component (no authentication required).
=> /register: Renders the Register component (no authentication required).
=> / (Home route): Renders the Home component. This route is protected and requires authentication (beforeEnter: checkAuth).
=> /student: Renders the StudentList component, showing the list of students. This route is also protected (beforeEnter: checkAuth).
=> /student/new: Renders the StudentAdd component, used to add new students. This route is protected as well (beforeEnter: checkAuth).

==================================================================================================

### Login.vue Component ###
This component provides the user interface and functionality for the login page of the application.

### Template Structure ###
=> Container: The entire login form is wrapped in a login-container div that is centrally aligned using absolute positioning and CSS transforms.
## Form: ##
=> Email Input: A form field for the user to enter their email address, bound to the email data property using v-model.
=> Password Input: A form field for the user to enter their password, bound to the password data property using v-model.
=> Error Display: Shows validation error messages or general errors (e.g., invalid credentials) conditionally using v-if.
=> Login Button: Submits the form when clicked, calling the login method.
=> Register Link: Provides a link to the registration page using router-link.

### Script Section ###
## Data: ##
=> email: Stores the user's email.
=> password: Stores the user's password.
=> error: A general error message for invalid login attempts.
=> emailError and passwordError: Store validation messages for respective fields.

## Methods: ##
=> validate(): Performs client-side validation for the email and password fields. It checks if the fields are filled out correctly, ensuring that the email format is valid and the password has a minimum length of 6 characters.
=> validEmail(): A helper function to validate the email format using a regular expression.
=> login(): Attempts to log in the user by sending a POST request to the /api/login endpoint. If the login is successful, the token is stored in localStorage, and the user is redirected to the home page. If the login fails (e.g., invalid credentials), an error message is displayed.
### Styling ###
=> The login-container is centrally aligned and styled with padding and border. The button is styled with a blue background that changes on hover for better user experience.

### Register.vue Component ###
This component provides the user interface and functionality for the registration page of the application.

## Template Structure ##
=> Container: The registration form is wrapped in a register-container div, centrally aligned using absolute positioning and CSS transforms.
## Form: ##
=> Name Input: A form field for the user to enter their name, bound to the name data property using v-model.
=> Email Input: A form field for the user to enter their email, bound to the email data property using v-model.
=> Password Input: A form field for the user to enter their password, bound to the password data property using v-model.
=> Password Confirmation Input: A form field for the user to confirm their password, bound to the passwordConfirmation data property.
=> Error Display: Shows validation error messages conditionally using v-if.
=> Register Button: Submits the form when clicked, calling the register method.
=> Login Link: Provides a link to the login page using router-link.

### Script Section ###
## Data: ##
=> name: Stores the user's name.
=> email: Stores the user's email.
=> password: Stores the user's password.
=> passwordConfirmation: Stores the password confirmation input.
=> error: A general error message for failed registration attempts.
=> nameError, emailError, passwordError, passwordConfirmationError: Store validation messages for respective fields.
### Methods: ###
=> validate(): Performs client-side validation for the name, email, password, and password confirmation fields. It checks if the fields are correctly filled out, ensuring the email format is valid and the password meets the length requirement, as well as confirming that both password fields match.
=> validEmail(): A helper function to validate the email format using a regular expression.
=> register(): Attempts to register the user by sending a POST request to the /api/register endpoint. If the registration is successful, a token is stored in localStorage, and the user is redirected to the home page. In case of validation errors, the API responses are mapped to display relevant error messages.

### Styling ###
=> The register-container is centrally aligned with padding and border for a clean form layout. The button is styled similarly to the login form, with a blue background and a hover effect for improved user interaction.

========================================================

##########################################
##      Backend Side Developemnt       ##
##########################################

### API Routes (routes/api.php) ### 
=> Login: POST /api/login – Authenticates the user using credentials and returns an API token.
=> Register: POST /api/register – Registers a new user, logs them in automatically, and returns an API token.
=> User Information: GET /api/user – Retrieves the currently authenticated user’s data using the auth:sanctum middleware.
=> Logout: POST /api/logout – Logs out the user by revoking their token using the auth:sanctum middleware.
=> Students API:
    => GET /api/students – Fetches all students (protected route).
    => POST /api/students – Adds a new student (protected route).
These routes leverage Sanctum for token-based authentication and authorization.


### HomeController.php ###
=> This controller handles login, registration, and logout functionalities.

### Methods: ###
=> index(): Returns the authenticated user's data if logged in, otherwise returns false. This is a helper method to check authentication status.

### login(Request $request): ###

=> Takes in the user’s email and password via a POST request.
=> Uses Auth::attempt() to check credentials and logs the user in if they are correct.
=> If login is successful, it generates an authentication token using Sanctum (createToken('authToken')->plainTextToken) and returns it in the response.
=> If login fails, a 401 response is returned with an "Invalid credentials" message.

### register(Request $request): ###

=> Validates the incoming request data to ensure the name, email, and password fields are correctly formatted and that the email is unique.
=> If validation fails, a 422 response with the validation errors is returned.
=> If validation passes, a new user is created in the database with a hashed password using Hash::make().
=> The newly registered user is logged in automatically, and an authentication token is generated and returned in the response.
=> If there's an issue logging in after registration, a 500 response is returned.

### logout(Request $request): ###

=> This method revokes the user's current token using tokens()->delete(), effectively logging them out.
=> Returns a success message upon successful logout.

### Token Authentication (Sanctum) ###
=> In both login and register methods, Sanctum is used for issuing tokens to authenticate API requests. After login or registration, the client receives a token, which is stored locally (e.g., in localStorage on the front end) and used in subsequent API requests to protected routes like /api/students.

### Notes: ### 
=> Validation: The Validator::make() function is used in the registration method to ensure valid data is submitted (e.g., email format, unique email, password length).
=> Password Hashing: Hash::make() is used to hash user passwords before storing them in the database for security purposes.
=> Token Management: On login and registration, a new token is generated for the user. This token is later used for authenticated requests (e.g., accessing the students' API). The logout() method deletes these tokens to log the user out.
