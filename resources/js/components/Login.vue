<template style="position: relative;width: 100%;height: 100%">
    <div class="login-container" style="position: absolute;top:50%;left: 50%;  transform: translate(-50%, -50%);padding:18px">
        <h1>Login</h1>

        <div v-if="error" class="alert alert-danger mt-3">
            {{ error }}
        </div>

        <form @submit.prevent="login">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" v-model="email" >
                 <span v-if="emailError" class="text-danger">{{ emailError }}</span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" v-model="password" >
                <span v-if="passwordError" class="text-danger">{{ passwordError }}</span>
            </div>

            <button type="submit">Login</button>

        </form>

         <div class="register-link">
            <p>Don't have an account? <router-link to="/register">Register here</router-link></p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            email: '',
            password: '',
            error: '',
            emailError: '',
            passwordError: ''
        };
    },
    methods: {
        validate() {
            this.emailError = '';
            this.passwordError = '';
            let isValid = true;

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

            return isValid;
        },
        validEmail(email) {
            // Basic email regex for validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        },
        async login() {
            if (!this.validate()) {
                return; // Prevent the login process if validation fails
            }

            try {
                const response = await axios.post('/api/login', {
                    email: this.email,
                    password: this.password
                });

                console.log('Login response:', response);

                const token = response.data.token;
                if (token) {
                    console.log('Token received:', token);
                    localStorage.setItem('authToken', token);
                    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                    this.$router.push('/');
                } else {
                    console.error('Token not found in response');
                }
            } catch (error) {
                this.error = 'Invalid credentials';
            }
        }
    }
}
</script>

<style scoped>
.login-container {
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
