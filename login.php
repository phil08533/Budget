<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FutureWorth - Login</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: linear-gradient(135deg, #0052cc 0%, #3385ff 100%);
    }
    .auth-container {
      width: 100%;
      max-width: 400px;
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      padding: 40px;
    }
    .auth-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .auth-header h1 {
      margin: 0;
      font-size: 2rem;
      color: #0052cc;
    }
    .auth-header p {
      color: #666;
      margin: 8px 0 0 0;
    }
    .auth-form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    .auth-form label {
      font-weight: 600;
      color: #1a1a1a;
      font-size: 0.9rem;
    }
    .auth-form input {
      padding: 12px;
      border: 1px solid #c0c7d8;
      border-radius: 10px;
      font-size: 0.95rem;
      transition: all 0.2s ease;
    }
    .auth-form input:focus {
      outline: none;
      border-color: #0052cc;
      box-shadow: 0 0 0 3px rgba(0, 82, 204, 0.1);
    }
    .auth-error {
      background: #ffe6e6;
      color: #cc0000;
      padding: 12px;
      border-radius: 10px;
      font-size: 0.9rem;
      display: none;
      margin-bottom: 10px;
    }
    .auth-error.show {
      display: block;
    }
    .auth-success {
      background: #e6ffe6;
      color: #006600;
      padding: 12px;
      border-radius: 10px;
      font-size: 0.9rem;
      display: none;
      margin-bottom: 10px;
    }
    .auth-success.show {
      display: block;
    }
    .auth-button {
      padding: 12px;
      background: linear-gradient(135deg, #0052cc 0%, #0052cc 100%);
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: 700;
      cursor: pointer;
      font-size: 0.95rem;
      transition: all 0.2s ease;
    }
    .auth-button:hover {
      box-shadow: 0 4px 16px rgba(0, 82, 204, 0.25);
      transform: translateY(-1px);
    }
    .auth-toggle {
      text-align: center;
      color: #666;
      font-size: 0.9rem;
      margin-top: 20px;
      border-top: 1px solid #e0e0e0;
      padding-top: 20px;
    }
    .auth-toggle button {
      background: none;
      border: none;
      color: #0052cc;
      font-weight: 700;
      cursor: pointer;
      text-decoration: underline;
      font-size: 0.9rem;
    }
    .auth-toggle button:hover {
      color: #0052cc;
    }
    .demo-login {
      background: #f0f7ff;
      border: 2px dashed #0052cc;
      border-radius: 12px;
      padding: 15px;
      margin-top: 20px;
      text-align: center;
    }
    .demo-login p {
      margin: 0 0 10px 0;
      font-size: 0.85rem;
      color: #666;
    }
    .demo-login button {
      background: #0052cc;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.85rem;
    }
    .form-group {
      display: none;
    }
    .form-group.show {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
  </style>
</head>
<body>
  <div class="auth-container">
    <div class="auth-header">
      <svg width="60" height="60" viewBox="0 0 40 40" style="fill: #0052cc; margin: 0 auto 15px; display: block;">
        <path d="M20 2C20 2 10 10 10 20C10 27.7 15 35 20 35C25 35 30 27.7 30 20C30 10 20 2 20 2Z" fill="#0052cc"/>
        <circle cx="20" cy="20" r="6" fill="white"/>
        <path d="M12 28C12 28 8 32 5 35M28 28C28 28 32 32 35 35" stroke="#0052cc" stroke-width="2" fill="none"/>
      </svg>
      <h1>Runway</h1>
      <p id="headerSubtitle">Login to your account</p>
    </div>

    <div id="errorMessage" class="auth-error"></div>
    <div id="successMessage" class="auth-success"></div>

    <!-- LOGIN FORM -->
    <form id="loginForm" class="auth-form">
      <div>
        <label for="loginEmail">Email</label>
        <input type="email" id="loginEmail" name="email" placeholder="your@email.com" required />
      </div>
      <div>
        <label for="loginPassword">Password</label>
        <input type="password" id="loginPassword" name="password" placeholder="••••••••" required />
      </div>
      <button type="submit" class="auth-button">Login</button>
    </form>

    <!-- REGISTER FORM -->
    <form id="registerForm" class="auth-form" style="display: none;">
      <div>
        <label for="registerName">Full Name</label>
        <input type="text" id="registerName" name="username" placeholder="John Doe" required />
      </div>
      <div>
        <label for="registerEmail">Email</label>
        <input type="email" id="registerEmail" name="email" placeholder="your@email.com" required />
      </div>
      <div>
        <label for="registerPassword">Password</label>
        <input type="password" id="registerPassword" name="password" placeholder="••••••••" minlength="6" required />
      </div>
      <div>
        <label for="registerConfirm">Confirm Password</label>
        <input type="password" id="registerConfirm" name="confirm_password" placeholder="••••••••" minlength="6" required />
      </div>
      <button type="submit" class="auth-button">Create Account</button>
    </form>

    <!-- TOGGLE BETWEEN LOGIN AND REGISTER -->
    <div class="auth-toggle">
      <div id="loginToggle">
        Don't have an account? <button type="button" onclick="toggleForms()">Sign up</button>
      </div>
      <div id="registerToggle" style="display: none;">
        Already have an account? <button type="button" onclick="toggleForms()">Login</button>
      </div>
    </div>

    <!-- DEMO LOGIN -->
    <div class="demo-login">
      <p>Want to try it out first?</p>
      <button type="button" onclick="demoLogin()">Login as Demo User</button>
    </div>
  </div>

  <script>
    // Initialize theme from localStorage
    const savedTheme = localStorage.getItem('futureworth-theme') || 'blue';
    document.documentElement.setAttribute('data-theme', savedTheme);

    function toggleForms() {
      const loginForm = document.getElementById('loginForm');
      const registerForm = document.getElementById('registerForm');
      const loginToggle = document.getElementById('loginToggle');
      const registerToggle = document.getElementById('registerToggle');
      const headerSubtitle = document.getElementById('headerSubtitle');

      loginForm.style.display = loginForm.style.display === 'none' ? 'flex' : 'none';
      registerForm.style.display = registerForm.style.display === 'none' ? 'flex' : 'none';
      loginToggle.style.display = loginToggle.style.display === 'none' ? 'block' : 'none';
      registerToggle.style.display = registerToggle.style.display === 'none' ? 'block' : 'none';
      headerSubtitle.textContent = loginForm.style.display === 'none' ? 'Create your account' : 'Login to your account';
    }

    function showMessage(message, type) {
      const errorEl = document.getElementById('errorMessage');
      const successEl = document.getElementById('successMessage');
      errorEl.classList.remove('show');
      successEl.classList.remove('show');

      if (type === 'error') {
        errorEl.textContent = message;
        errorEl.classList.add('show');
      } else {
        successEl.textContent = message;
        successEl.classList.add('show');
      }
    }

    document.getElementById('loginForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = document.getElementById('loginEmail').value;
      const password = document.getElementById('loginPassword').value;

      try {
        const response = await fetch('auth.php?action=login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password })
        });

        const data = await response.json();
        if (data.success) {
          showMessage('Login successful! Redirecting...', 'success');
          setTimeout(() => window.location.href = 'index.php', 1000);
        } else {
          showMessage(data.error || 'Login failed', 'error');
        }
      } catch (err) {
        showMessage('Error: ' + err.message, 'error');
      }
    });

    document.getElementById('registerForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const username = document.getElementById('registerName').value;
      const email = document.getElementById('registerEmail').value;
      const password = document.getElementById('registerPassword').value;
      const confirm = document.getElementById('registerConfirm').value;

      if (password !== confirm) {
        showMessage('Passwords do not match', 'error');
        return;
      }

      try {
        const response = await fetch('auth.php?action=register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ username, email, password })
        });

        const data = await response.json();
        if (data.success) {
          showMessage('Account created! Logging in...', 'success');
          setTimeout(() => window.location.href = 'index.php', 1000);
        } else {
          showMessage(data.error || 'Registration failed', 'error');
        }
      } catch (err) {
        showMessage('Error: ' + err.message, 'error');
      }
    });

    function demoLogin() {
      fetch('auth.php?action=demo', { method: 'POST' })
        .then(() => window.location.href = 'index.php')
        .catch(err => showMessage('Error: ' + err.message, 'error'));
    }
  </script>
</body>
</html>
