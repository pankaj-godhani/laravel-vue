<template>
    <div class="register-container" style="position: absolute;top:50%;left: 50%;  transform: translate(-50%, -50%);padding:18px">
        <h1>Register</h1>

        <div v-if="error" class="alert alert-danger mt-3">
            {{ error }}
        </div>

        <form @submit.prevent="register">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" v-model="name" />
                <span v-if="nameError" class="text-danger">{{ nameError }}</span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" v-model="email" />
                <span v-if="emailError" class="text-danger">{{ emailError }}</span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" v-model="password" />
                <span v-if="passwordError" class="text-danger">{{ passwordError }}</span>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" class="form-control" v-model="passwordConfirmation" />
                <span v-if="passwordConfirmationError" class="text-danger">{{ passwordConfirmationError }}</span>
            </div>

            <button type="submit">Register</button>
        </form>

        <div class="login-link">
            <p>Already have an account? <router-link to="/login">Login here</router-link></p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            name: '',
            email: '',
            password: '',
            passwordConfirmation: '',
            error: '',
            nameError: '',
            emailError: '',
            passwordError: '',
            passwordConfirmationError: ''
        };
    },
    methods: {
        validate() {
            this.nameError = '';
            this.emailError = '';
            this.passwordError = '';
            this.passwordConfirmationError = '';
            let isValid = true;

            // Name validation
            if (!this.name) {
                this.nameError = 'Name is required.';
                isValid = false;
            }

            // Email validation
            if (!this.email) {
                this.emailError = 'Email is required.';
                isValid = false;
            } else if (!this.validEmail(this.email)) {
                this.emailError = 'Invalid email format.';
                isValid = false;
            }

            // Password validation
            if (!this.password) {
                this.passwordError = 'Password is required.';
                isValid = false;
            } else if (this.password.length < 6) {
                this.passwordError = 'Password must be at least 6 characters long.';
                isValid = false;
            }

            // Password confirmation validation
            if (this.password !== this.passwordConfirmation) {
                this.passwordConfirmationError = 'Passwords do not match.';
                isValid = false;
            }

            return isValid;
        },
        validEmail(email) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        },
        async register() {
            if (!this.validate()) {
                return; // Prevent the registration process if validation fails
            }

            try {
                const response = await axios.post('/api/register', {
                    name: this.name,
                    email: this.email,
                    password: this.password
                });

                // If the registration is successful
                if (response.status === 200) {
                    // Save the token (optional: for authenticated routes)
                    localStorage.setItem('authToken', response.data.token);

                    // Redirect to home page
                    this.$router.push({ path: '/' });
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Handle validation errors
                    const errors = error.response.data.errors;
                    this.nameError = errors.name ? errors.name[0] : '';
                    this.emailError = errors.email ? errors.email[0] : '';
                    this.passwordError = errors.password ? errors.password[0] : '';
                } else {
                    // Generic error message
                    this.error = 'Registration failed. Please try again.';
                }
            }
        }
    }

}
</script>

<style scoped>
.register-container {
    max-width: 500px;
    margin: auto;
    padding: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 20%;
}

.form-group {
    margin-bottom: 1rem;
}

form button {
    display: block;
    width: 100%;
    padding: 0.5rem;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
form button:hover {
    background-color: #0056b3;
}
</style>
