<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();
$clients = $pdo->query("SELECT * FROM clients ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Клиенты - Ортопедический салон</title>
    <style>
        /* Стили из предыдущих файлов */
        :root {
            --primary-color: #009AA6;
            --accent-color: #FF6E00;
            --light-gray: #f5f5f5;
            --dark-gray: #333;
            --white: #ffffff;
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
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: var(--white);
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
        }

        .logo {
            text-align: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 120px;
            height: auto;
        }

        .logo h1 {
            color: var(--primary-color);
            font-size: 18px;
            margin-top: 10px;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--dark-gray);
            text-decoration: none;
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: var(--white);
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-name {
            margin-right: 15px;
            font-weight: 500;
        }

        .logout-btn {
            background-color: var(--accent-color);
            color: var(--white);
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .content {
            padding: 30px;
            flex: 1;
        }

        .section-title {
            color: var(--primary-color);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #007a83;
        }

        .btn-accent {
            background-color: var(--accent-color);
        }

        .btn-accent:hover {
            background-color: #e05a00;
        }

        .table-container {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: var(--primary-color);
            color: var(--white);
            font-weight: 500;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: var(--white);
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .search-bar {
            background-color: var(--white);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: end;
        }

        .search-group {
            flex: 1;
        }

        .btn-danger {
            background-color: #dc3545;
            color: var(--white);
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Добавьте эти стили в каждый CSS раздел */
        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            z-index: 10000;
            font-weight: 500;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease;
        }

        .message-success {
            background-color: #28a745;
        }

        .message-error {
            background-color: #dc3545;
        }

        .message-info {
            background-color: #17a2b8;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Боковая панель -->
    <div class="sidebar">
        <div class="logo">
            <img src="лого.png" alt="Логотип ортопедического салона">
            <h1>Ортопедический салон</h1>
        </div>

        <ul class="nav-menu">
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><i>📊</i> Панель управления</a></li>
            <li class="nav-item"><a href="appointments.php" class="nav-link"><i>📅</i> Записи на прием</a></li>
            <li class="nav-item"><a href="clients.php" class="nav-link active"><i>👥</i> Клиенты</a></li>
            <li class="nav-item"><a href="orders.php" class="nav-link"><i>📋</i> Заказы</a></li>
            <li class="nav-item"><a href="schedule.php" class="nav-link"><i>⏰</i> График работы</a></li>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li class="nav-item"><a href="employees.php" class="nav-link"><i>⚙️</i> Сотрудники</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Основной контент -->
    <div class="main-content">
        <div class="header">
            <h2>Управление клиентами</h2>
            <div class="user-info">
                <div class="user-name">
                    <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                    (<?php echo $_SESSION['role'] == 'admin' ? 'Администратор' : 'Сотрудник'; ?>)
                </div>
                <a href="logout.php" class="logout-btn">Выйти</a>
            </div>
        </div>

        <div class="content">
            <div class="section-title">
                <h2>Список клиентов</h2>
                <button class="btn btn-accent" onclick="openModal()">+ Новый клиент</button>
            </div>

            <div class="search-bar">
                <div class="search-group">
                    <label>Поиск по ФИО</label>
                    <input type="text" id="searchName" placeholder="Введите ФИО клиента...">
                </div>
                <div class="search-group">
                    <label>Телефон</label>
                    <input type="text" id="searchPhone" placeholder="Введите телефон...">
                </div>
                <button class="btn" onclick="searchClients()">Найти</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ФИО</th>
                            <th>Телефон</th>
                            <th>Email</th>
                            <th>Адрес</th>
                            <th>Мед. история</th>
                            <th>Дата регистрации</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($client['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($client['phone']); ?></td>
                                <td><?php echo htmlspecialchars($client['email']); ?></td>
                                <td><?php echo htmlspecialchars($client['address']); ?></td>
                                <td>
                                    <?php if ($client['medical_history']): ?>
                                        <button class="btn btn-sm"
                                            onclick="showMedicalHistory(<?php echo $client['id']; ?>)">Просмотр</button>
                                    <?php else: ?>
                                        <span style="color: #999;">Нет данных</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d.m.Y', strtotime($client['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm"
                                            onclick="editClient(<?php echo $client['id']; ?>)">Редактировать</button>
                                        <button class="btn btn-sm btn-accent"
                                            onclick="createAppointment(<?php echo $client['id']; ?>)">Запись</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Модальное окно добавления клиента -->
    <div id="clientModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Новый клиент</h3>
            <form id="clientForm" method="POST" action="save_client.php">
                <input type="hidden" id="clientId" name="id">

                <div class="form-group">
                    <label for="full_name">ФИО *</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>

                <div class="form-group">
                    <label for="address">Адрес</label>
                    <textarea id="address" name="address" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="medical_history">Медицинская история</label>
                    <textarea id="medical_history" name="medical_history" rows="5"
                        placeholder="Диагнозы, рекомендации врачей, особенности..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Отмена</button>
                    <button type="submit" class="btn btn-accent">Сохранить</button>
                </div>
                <!-- В таблице клиентов добавьте кнопку удаления -->
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm"
                            onclick="editClient(<?php echo $client['id']; ?>)">Редактировать</button>
                        <button class="btn btn-sm btn-accent"
                            onclick="createAppointment(<?php echo $client['id']; ?>)">Запись</button>
                        <button class="btn btn-sm btn-danger"
                            onclick="deleteClient(<?php echo $client['id']; ?>)">Удалить</button>
                    </div>
                </td>
            </form>
        </div>
    </div>

    <script>
        let currentClientId = null;

        function openModal(id = null) {
            const modal = document.getElementById('clientModal');
            modal.style.display = 'block';

            if (id) {
                currentClientId = id;
                document.getElementById('modalTitle').textContent = 'Редактировать клиента';
                loadClientData(id);
            } else {
                currentClientId = null;
                document.getElementById('modalTitle').textContent = 'Новый клиент';
                document.getElementById('clientForm').reset();
            }
        }

        function closeModal() {
            document.getElementById('clientModal').style.display = 'none';
            currentClientId = null;
        }

        function loadClientData(id) {
            fetch(`get_client.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('clientId').value = data.client.id;
                        document.getElementById('full_name').value = data.client.full_name;
                        document.getElementById('phone').value = data.client.phone;
                        document.getElementById('email').value = data.client.email;
                        document.getElementById('address').value = data.client.address;
                        document.getElementById('medical_history').value = data.client.medical_history;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при загрузке данных');
                });
        }

        function editClient(id) {
            openModal(id);
        }

        function showMedicalHistory(id) {
            fetch(`get_client.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Медицинская история:\n\n' + (data.client.medical_history || 'Нет данных'));
                    }
                });
        }

        function deleteClient(id) {
            if (confirm('Вы уверены, что хотите удалить клиента?')) {
                fetch('delete_client.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert('Ошибка: ' + data.message);
                        }
                    });
            }
        }

        // Обработка формы клиента
        document.getElementById('clientForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('id', currentClientId);

            fetch('save_client.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        closeModal();
                        location.reload();
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при сохранении');
                });
        });

        // Поиск клиентов
        function searchClients() {
            const name = document.getElementById('searchName').value;
            const phone = document.getElementById('searchPhone').value;

            fetch(`search_clients.php?name=${encodeURIComponent(name)}&phone=${encodeURIComponent(phone)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateClientsTable(data.clients);
                    }
                });
        }

        function updateClientsTable(clients) {
            // Здесь нужно обновить таблицу клиентов
            console.log('Update table with:', clients);
            // Для простоты перезагружаем страницу
            location.reload();
        }

        // Закрытие модального окна
        window.onclick = function (event) {
            const modal = document.getElementById('clientModal');
            if (event.target == modal) {
                closeModal();
            }
        }
        let currentClientId = null;

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function () {
            loadSearchFilters();
            setupRealTimeSearch();
        });

        // Открытие модального окна
        function openModal(id = null) {
            const modal = document.getElementById('clientModal');
            modal.style.display = 'block';

            if (id) {
                currentClientId = id;
                document.getElementById('modalTitle').textContent = 'Редактировать клиента';
                loadClientData(id);
            } else {
                currentClientId = null;
                document.getElementById('modalTitle').textContent = 'Новый клиент';
                document.getElementById('clientForm').reset();
            }
        }

        // Закрытие модального окна
        function closeModal() {
            document.getElementById('clientModal').style.display = 'none';
            currentClientId = null;
        }

        // Загрузка данных клиента
        function loadClientData(id) {
            showLoading();
            fetch(`get_client.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        document.getElementById('clientId').value = data.client.id;
                        document.getElementById('full_name').value = data.client.full_name || '';
                        document.getElementById('phone').value = data.client.phone || '';
                        document.getElementById('email').value = data.client.email || '';
                        document.getElementById('address').value = data.client.address || '';
                        document.getElementById('medical_history').value = data.client.medical_history || '';
                    } else {
                        showMessage('error', data.message);
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showMessage('error', 'Ошибка при загрузке данных');
                });
        }

        // Редактирование клиента
        function editClient(id) {
            openModal(id);
        }

        // Просмотр медицинской истории
        function showMedicalHistory(id) {
            showLoading();
            fetch(`get_client.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        const history = data.client.medical_history || 'Нет данных';
                        openMedicalHistoryModal(data.client.full_name, history);
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showMessage('error', 'Ошибка при загрузке данных');
                });
        }

        // Открытие модального окна с медицинской историей
        function openMedicalHistoryModal(clientName, history) {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.id = 'medicalHistoryModal';
            modal.style.cssText = `
            display: block;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        `;

            modal.innerHTML = `
            <div class="modal-content" style="max-width: 600px; max-height: 80vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="color: var(--primary-color); margin: 0;">Медицинская история: ${escapeHtml(clientName)}</h3>
                    <button onclick="closeMedicalHistoryModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">×</button>
                </div>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; min-height: 200px; white-space: pre-wrap; line-height: 1.6;">
                    ${escapeHtml(history)}
                </div>
                <div style="margin-top: 20px; text-align: right;">
                    <button class="btn" onclick="closeMedicalHistoryModal()">Закрыть</button>
                </div>
            </div>
        `;

            document.body.appendChild(modal);
        }

        // Закрытие модального окна с медицинской историей
        function closeMedicalHistoryModal() {
            const modal = document.getElementById('medicalHistoryModal');
            if (modal) {
                modal.remove();
            }
        }

        // Создание записи для клиента
        function createAppointment(clientId) {
            // Сохраняем ID клиента в localStorage для использования на странице записей
            localStorage.setItem('selectedClientId', clientId);
            window.location.href = 'appointments.php';
        }

        // Удаление клиента
        function deleteClient(id, clientName) {
            if (confirm(`Вы уверены, что хотите удалить клиента "${clientName}"? Это действие нельзя отменить.`)) {
                showLoading();
                fetch('delete_client.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.success) {
                            showMessage('success', data.message);
                            // Удаляем строку из таблицы
                            document.querySelector(`tr[data-id="${id}"]`)?.remove();
                            // Если таблица пуста, показываем сообщение
                            if (document.querySelectorAll('table tbody tr').length === 0) {
                                showEmptyTableMessage();
                            }
                        } else {
                            showMessage('error', data.message);
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        console.error('Error:', error);
                        showMessage('error', 'Ошибка при удалении');
                    });
            }
        }

        // Поиск клиентов
        function searchClients() {
            const name = document.getElementById('searchName').value;
            const phone = document.getElementById('searchPhone').value;
            const email = document.getElementById('searchEmail').value;

            const params = new URLSearchParams();
            if (name) params.append('name', name);
            if (phone) params.append('phone', phone);
            if (email) params.append('email', email);

            showLoading();
            fetch(`search_clients.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        updateClientsTable(data.clients);
                        saveSearchFilters();
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showMessage('error', 'Ошибка при поиске');
                });
        }

        // Очистка поиска
        function clearSearch() {
            document.getElementById('searchName').value = '';
            document.getElementById('searchPhone').value = '';
            document.getElementById('searchEmail').value = '';
            localStorage.removeItem('clientSearchFilters');
            searchClients();
        }

        // Настройка поиска в реальном времени
        function setupRealTimeSearch() {
            let searchTimeout;
            const searchInputs = ['searchName', 'searchPhone', 'searchEmail'];

            searchInputs.forEach(inputId => {
                document.getElementById(inputId).addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(searchClients, 500);
                });
            });
        }

        // Сохранение фильтров поиска
        function saveSearchFilters() {
            const filters = {
                name: document.getElementById('searchName').value,
                phone: document.getElementById('searchPhone').value,
                email: document.getElementById('searchEmail').value
            };
            localStorage.setItem('clientSearchFilters', JSON.stringify(filters));
        }

        // Загрузка фильтров поиска
        function loadSearchFilters() {
            const savedFilters = localStorage.getItem('clientSearchFilters');
            if (savedFilters) {
                const filters = JSON.parse(savedFilters);
                document.getElementById('searchName').value = filters.name || '';
                document.getElementById('searchPhone').value = filters.phone || '';
                document.getElementById('searchEmail').value = filters.email || '';
            }
            // Выполняем поиск после загрузки фильтров
            searchClients();
        }

        // Обновление таблицы клиентов
        function updateClientsTable(clients) {
            const tbody = document.querySelector('table tbody');
            tbody.innerHTML = '';

            if (clients.length === 0) {
                showEmptyTableMessage();
                return;
            }

            clients.forEach(client => {
                const row = document.createElement('tr');
                row.dataset.id = client.id;

                const hasMedicalHistory = client.medical_history && client.medical_history.trim().length > 0;

                row.innerHTML = `
                <td>
                    <div style="font-weight: 500;">${escapeHtml(client.full_name)}</div>
                    ${client.email ? `<div style="font-size: 12px; color: #666;">${escapeHtml(client.email)}</div>` : ''}
                </td>
                <td>${escapeHtml(client.phone || '—')}</td>
                <td>${escapeHtml(client.email || '—')}</td>
                <td>${escapeHtml(client.address ? client.address.substring(0, 50) + (client.address.length > 50 ? '...' : '') : '—')}</td>
                <td>
                    ${hasMedicalHistory ?
                        `<button class="btn btn-sm" onclick="showMedicalHistory(${client.id})" title="Просмотр истории">
                            📄 Просмотр
                        </button>` :
                        `<span style="color: #999; font-size: 14px;">Нет данных</span>`
                    }
                </td>
                <td>${formatDate(client.created_at)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm" onclick="editClient(${client.id})" title="Редактировать">
                            ✏️
                        </button>
                        <button class="btn btn-sm btn-accent" onclick="createAppointment(${client.id})" title="Создать запись">
                            📅
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteClient(${client.id}, '${escapeHtml(client.full_name)}')" title="Удалить">
                            🗑️
                        </button>
                    </div>
                </td>
            `;

                tbody.appendChild(row);
            });
        }

        // Показать сообщение о пустой таблице
        function showEmptyTableMessage() {
            const tbody = document.querySelector('table tbody');
            tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 60px 20px;">
                    <div style="max-width: 400px; margin: 0 auto;">
                        <div style="font-size: 48px; margin-bottom: 20px;">👥</div>
                        <div style="font-size: 18px; color: #666; margin-bottom: 10px; font-weight: 500;">
                            Клиенты не найдены
                        </div>
                        <div style="color: #999; margin-bottom: 20px;">
                            Попробуйте изменить параметры поиска или добавьте нового клиента
                        </div>
                        <button class="btn btn-accent" onclick="openModal()">
                            + Добавить первого клиента
                        </button>
                    </div>
                </td>
            </tr>
        `;
        }

        // Обработка формы клиента
        document.getElementById('clientForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            if (currentClientId) {
                formData.append('id', currentClientId);
            }

            // Показываем индикатор загрузки
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Сохранение...';
            submitBtn.disabled = true;

            fetch('save_client.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('success', data.message);
                        closeModal();
                        searchClients(); // Обновляем таблицу
                    } else {
                        showMessage('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('error', 'Ошибка при сохранении');
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
        });

        // Вспомогательные функции
        function formatDate(dateString) {
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('ru-RU');
            } catch (e) {
                return dateString;
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showMessage(type, text) {
            // Используем ту же функцию, что и в appointments.php
            const messageDiv = document.createElement('div');
            messageDiv.className = `message message-${type}`;
            messageDiv.textContent = text;
            messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            z-index: 10000;
            font-weight: 500;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
        `;

            if (type === 'success') {
                messageDiv.style.backgroundColor = '#28a745';
            } else if (type === 'error') {
                messageDiv.style.backgroundColor = '#dc3545';
            } else {
                messageDiv.style.backgroundColor = '#17a2b8';
            }

            document.body.appendChild(messageDiv);

            setTimeout(() => {
                messageDiv.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => messageDiv.remove(), 300);
            }, 5000);
        }

        function showLoading() {
            let loadingDiv = document.getElementById('loadingOverlay');
            if (!loadingDiv) {
                loadingDiv = document.createElement('div');
                loadingDiv.id = 'loadingOverlay';
                loadingDiv.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255,255,255,0.8);
                z-index: 9999;
                display: flex;
                justify-content: center;
                align-items: center;
            `;
                loadingDiv.innerHTML = `
                <div style="text-align: center;">
                    <div style="font-size: 40px; margin-bottom: 10px;">⏳</div>
                    <div style="color: #666;">Загрузка...</div>
                </div>
            `;
                document.body.appendChild(loadingDiv);
            } else {
                loadingDiv.style.display = 'flex';
            }
        }

        function hideLoading() {
            const loadingDiv = document.getElementById('loadingOverlay');
            if (loadingDiv) {
                loadingDiv.style.display = 'none';
            }
        }

        // Добавляем стили для анимации
        const style = document.createElement('style');
        style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        .search-bar {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .search-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        .search-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
    `;
        document.head.appendChild(style);

        // Закрытие модального окна при клике вне его
        window.onclick = function (event) {
            const modal = document.getElementById('clientModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>