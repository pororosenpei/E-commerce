<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Modal</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            min-height: 100vh;
            background-color: #f7fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .container {
            max-width: 400px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white */
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: block; /* Show by default */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000; /* Ensure it appears above other elements */
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            z-index: 999; /* Behind the modal */
            display: block; /* Show by default */
        }
        h2 {
            text-align: center;
            font-size: 1.875rem; /* 3xl */
            font-weight: 800; /* extrabold */
            color: #1a202c; /* gray-900 */
        }
        p {
            text-align: center;
            margin-top: 0.5rem;
            color: #4a5568; /* gray-600 */
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            font-size: 0.875rem; /* sm */
            color: #4a5568; /* gray-600 */
            margin-bottom: 0.5rem;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #cbd5e0; /* gray-300 */
            border-radius: 0.375rem; /* md */
            font-size: 1rem; /* base */
            color: #1a202c; /* gray-900 */
        }
        input[type="checkbox"] {
            margin-right: 0.5rem;
        }
        button {
            width: 100%;
            padding: 0.5rem;
            border: none;
            border-radius: 0.375rem; /* md */
            color: white;
            background-color: #97A97C; /* Custom color */
            font-size: 1rem; /* base */
            font-weight: 500; /* medium */
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #879c6d; /* Custom hover color */
        }
        .divider {
            display: flex;
            align-items: center;
            margin: 1rem 0;
        }
        .divider span {
            margin: 0 0.5rem;
            background-color: white;
            color: #a0aec0; /* gray-500 */
            font-size: 0.875rem; /* sm */
        }
        .google-button {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e0; /* gray-300 */
            border-radius: 0.375rem; /* md */
            background-color: white;
            color: #4a5568; /* gray-500 */
            margin-top: 1rem;
        }
        .google-button svg {
            margin-right: 0.5rem;
        }
        .sign-up {
            text-align: center;
            margin-top: 1rem;
        }
        .sign-up a {
            color: #97A97C; /* Custom link color */
            text-decoration: none;
        }
        .sign-up a:hover {
            color: #879c6d; /* Custom hover link color */
        }
        .close-modal {
            cursor: pointer;
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            color: #4a5568; /* gray-600 */
        }
    </style>
</head>
<body>

    <div class="modal-overlay" id="modalOverlay"></div>

    <div class="container" id="loginModal">
        <span class="close-modal" id="closeModalButton">&times;</span>
        <h2>Log In</h2>
        <p>Please login to continue to your account.</p>
        <form>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" placeholder="Email address" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="checkbox" id="stay-signed-in">
                <label for="stay-signed-in">Stay signed in</label>
            </div>
            <button type="submit">Log In</button>
        </form>
        <div class="divider">
            <hr style="flex-grow: 1;">
            <span>Or continue with</span>
            <hr style="flex-grow: 1;">
        </div>
        <button class="google-button">
            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" width="20" height="20">
                <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z" />
            </svg>
            Sign in with Google
        </button>
        <p class="sign-up">
            Need an account? <a href="#">Create one</a>
        </p>
    </div>

    <script>
        const closeModalButton = document.getElementById('closeModalButton');
        const modalOverlay = document.getElementById('modalOverlay');
        const loginModal = document.getElementById('loginModal');

        closeModalButton.addEventListener('click', () => {
            loginModal.style.display = 'none';
            modalOverlay.style.display = 'none';
        });

        modalOverlay.addEventListener('click', () => {
            loginModal.style.display = 'none';
            modalOverlay.style.display = 'none';
        });
    </script>

</body>
</html>
