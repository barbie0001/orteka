<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ортопедический салон - Вход в систему</title>
    <style>
        :root {
            --primary-color: #009AA6;
            --accent-color: #FF6E00;
            --light-gray: #f5f5f5;
            --dark-gray: #333;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light-gray);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo img {
            max-width: 150px;
            height: auto;
        }
        
        .logo h1 {
            color: var(--primary-color);
            margin-top: 10px;
            font-size: 24px;
        }
        
        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .form-header h2 {
            color: var(--dark-gray);
            font-size: 22px;
        }
        
        .form-toggle {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .toggle-btn {
            flex: 1;
            padding: 10px;
            text-align: center;
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            color: var(--dark-gray);
            transition: all 0.3s;
        }
        
        .toggle-btn.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }
        
        .form {
            display: none;
        }
        
        .form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark-gray);
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #007a83;
        }
        
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            display: none;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <!-- Замените src на путь к вашему логотипу -->
            <img src="лого.png" alt="Логотип ортопедического салона">
            <h1>Ортопедический салон</h1>
        </div>
        
        <div class="form-container">
            <div class="form-header">
                <h2>Вход в систему</h2>
            </div>
            
            <div class="form-toggle">
                <button class="toggle-btn active" id="login-toggle">Вход</button>
                <button class="toggle-btn" id="register-toggle">Регистрация</button>
            </div>
            
            <form id="login-form" class="form active">
                <div class="form-group">
                    <label for="login-username">Имя пользователя</label>
                    <input type="text" id="login-username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="login-password">Пароль</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                
                <button type="submit" class="btn">Войти</button>
                
                <div id="login-message" class="message"></div>
            </form>
            
            <form id="register-form" class="form">
                <div class="form-group">
                    <label for="register-fullname">Полное имя</label>
                    <input type="text" id="register-fullname" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label for="register-username">Имя пользователя</label>
                    <input type="text" id="register-username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="register-phone">Телефон</label>
                    <input type="tel" id="register-phone" name="phone">
                </div>
                
                <div class="form-group">
                    <label for="register-password">Пароль</label>
                    <input type="password" id="register-password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="register-password-confirm">Подтверждение пароля</label>
                    <input type="password" id="register-password-confirm" name="password_confirm" required>
                </div>
                
                <button type="submit" class="btn">Зарегистрироваться</button>
                
                <div id="register-message" class="message"></div>
            </form>
        </div>
    </div>

    <script>
        // Переключение между формами входа и регистрации
        document.getElementById('login-toggle').addEventListener('click', function() {
            document.getElementById('login-form').classList.add('active');
            document.getElementById('register-form').classList.remove('active');
            document.getElementById('login-toggle').classList.add('active');
            document.getElementById('register-toggle').classList.remove('active');
        });
        
        document.getElementById('register-toggle').addEventListener('click', function() {
            document.getElementById('register-form').classList.add('active');
            document.getElementById('login-form').classList.remove('active');
            document.getElementById('register-toggle').classList.add('active');
            document.getElementById('login-toggle').classList.remove('active');
        });
        
        // Обработка формы входа
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('login-username').value;
            const password = document.getElementById('login-password').value;
            const messageDiv = document.getElementById('login-message');
            
            // AJAX запрос для входа
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'login.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.success) {
                            messageDiv.textContent = response.message;
                            messageDiv.className = 'message success';
                            messageDiv.style.display = 'block';
                            
                            // Перенаправление после успешного входа
                            setTimeout(function() {
                                window.location.href = 'dashboard.php';
                            }, 1000);
                        } else {
                            messageDiv.textContent = response.message;
                            messageDiv.className = 'message error';
                            messageDiv.style.display = 'block';
                        }
                    } else {
                        messageDiv.textContent = 'Ошибка соединения с сервером';
                        messageDiv.className = 'message error';
                        messageDiv.style.display = 'block';
                    }
                }
            };
            
            xhr.send(`username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`);
        });
        
        // Обработка формы регистрации
        document.getElementById('register-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fullName = document.getElementById('register-fullname').value;
            const username = document.getElementById('register-username').value;
            const email = document.getElementById('register-email').value;
            const phone = document.getElementById('register-phone').value;
            const password = document.getElementById('register-password').value;
            const passwordConfirm = document.getElementById('register-password-confirm').value;
            const messageDiv = document.getElementById('register-message');
            
            // Проверка совпадения паролей
            if (password !== passwordConfirm) {
                messageDiv.textContent = 'Пароли не совпадают';
                messageDiv.className = 'message error';
                messageDiv.style.display = 'block';
                return;
            }
            
            // AJAX запрос для регистрации
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'register.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.success) {
                            messageDiv.textContent = response.message;
                            messageDiv.className = 'message success';
                            messageDiv.style.display = 'block';
                            
                            // Очистка формы после успешной регистрации
                            document.getElementById('register-form').reset();
                            
                            // Переключение на форму входа
                            setTimeout(function() {
                                document.getElementById('login-toggle').click();
                            }, 2000);
                        } else {
                            messageDiv.textContent = response.message;
                            messageDiv.className = 'message error';
                            messageDiv.style.display = 'block';
                        }
                    } else {
                        messageDiv.textContent = 'Ошибка соединения с сервером';
                        messageDiv.className = 'message error';
                        messageDiv.style.display = 'block';
                    }
                }
            };
            
            xhr.send(`full_name=${encodeURIComponent(fullName)}&username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}&password=${encodeURIComponent(password)}`);
        });
    </script>
</body>
</html>