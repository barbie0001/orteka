<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();
$appointments = $pdo->query("
    SELECT a.*, c.full_name as client_name, u.full_name as employee_name 
    FROM appointments a 
    LEFT JOIN clients c ON a.client_id = c.id 
    LEFT JOIN users u ON a.user_id = u.id 
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
")->fetchAll();

$clients = $pdo->query("SELECT id, full_name FROM clients ORDER BY full_name")->fetchAll();
$employees = $pdo->query("SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Записи на прием - Ортопедический салон</title>
    <style>
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
        
        .nav-link:hover, .nav-link.active {
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
        
        th, td {
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
        
        .status-planned { background-color: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; }
        .status-completed { background-color: #d1edff; color: #004085; padding: 4px 8px; border-radius: 4px; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; }
        
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
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: var(--white);
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input, .form-group select, .form-group textarea {
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
        
        .filter-bar {
            background-color: var(--white);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: end;
        }
        
        .filter-group {
            flex: 1;
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
            <li class="nav-item"><a href="appointments.php" class="nav-link active"><i>📅</i> Записи на прием</a></li>
            <li class="nav-item"><a href="clients.php" class="nav-link"><i>👥</i> Клиенты</a></li>
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
            <h2>Управление записями на прием</h2>
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
                <h2>Список записей</h2>
                <button class="btn btn-accent" onclick="openModal()">+ Новая запись</button>
            </div>
            
            <div class="filter-bar">
                <div class="form-group">
                    <label>Дата с</label>
                    <input type="date" id="filterDateFrom">
                </div>
                <div class="form-group">
                    <label>Дата по</label>
                    <input type="date" id="filterDateTo">
                </div>
                <div class="form-group">
                    <label>Статус</label>
                    <select id="filterStatus">
                        <option value="">Все</option>
                        <option value="запланирован">Запланирован</option>
                        <option value="завершен">Завершен</option>
                        <option value="отменен">Отменен</option>
                    </select>
                </div>
                <button class="btn" onclick="applyFilters()">Применить</button>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Время</th>
                            <th>Клиент</th>
                            <th>Сотрудник</th>
                            <th>Тип приема</th>
                            <th>Статус</th>
                            <th>Дата создания</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo date('d.m.Y', strtotime($appointment['appointment_date'])); ?></td>
                            <td><?php echo $appointment['appointment_time']; ?></td>
                            <td><?php echo htmlspecialchars($appointment['client_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['employee_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_type']); ?></td>
                            <td>
                                <span class="status-<?php echo $appointment['status']; ?>">
                                    <?php echo $appointment['status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($appointment['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm" onclick="editAppointment(<?php echo $appointment['id']; ?>)">Редактировать</button>
                                    <button class="btn btn-sm btn-accent" onclick="updateStatus(<?php echo $appointment['id']; ?>, 'completed')">Завершить</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно добавления записи -->
    <div id="appointmentModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Новая запись на прием</h3>
            <form id="appointmentForm" method="POST" action="save_appointment.php">
                <input type="hidden" id="appointmentId" name="id">
                
                <div class="form-group">
                    <label for="client_id">Клиент</label>
                    <select id="client_id" name="client_id" required>
                        <option value="">Выберите клиента</option>
                        <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client['id']; ?>"><?php echo htmlspecialchars($client['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="user_id">Сотрудник</label>
                    <select id="user_id" name="user_id" required>
                        <option value="">Выберите сотрудника</option>
                        <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo $employee['id']; ?>"><?php echo htmlspecialchars($employee['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="appointment_date">Дата приема</label>
                    <input type="date" id="appointment_date" name="appointment_date" required>
                </div>
                
                <div class="form-group">
                    <label for="appointment_time">Время приема</label>
                    <input type="time" id="appointment_time" name="appointment_time" required>
                </div>
                
                <div class="form-group">
                    <label for="appointment_type">Тип приема</label>
                    <select id="appointment_type" name="appointment_type" required>
                        <option value="консультация">Консультация</option>
                        <option value="замер">Замер</option>
                        <option value="примерка">Примерка</option>
                        <option value="выдача">Выдача</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Отмена</button>
                    <button type="submit" class="btn btn-accent">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

<script>
    let currentAppointmentId = null;
    
    function openModal(id = null) {
        const modal = document.getElementById('appointmentModal');
        modal.style.display = 'block';
        
        if (id) {
            currentAppointmentId = id;
            document.getElementById('modalTitle').textContent = 'Редактировать запись';
            loadAppointmentData(id);
        } else {
            currentAppointmentId = null;
            document.getElementById('modalTitle').textContent = 'Новая запись на прием';
            document.getElementById('appointmentForm').reset();
        }
    }
    
    function closeModal() {
        document.getElementById('appointmentModal').style.display = 'none';
        currentAppointmentId = null;
    }
    
    function loadAppointmentData(id) {
        fetch(`get_appointment.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('appointmentId').value = data.appointment.id;
                    document.getElementById('client_id').value = data.appointment.client_id;
                    document.getElementById('user_id').value = data.appointment.user_id;
                    document.getElementById('appointment_date').value = data.appointment.appointment_date;
                    document.getElementById('appointment_time').value = data.appointment.appointment_time;
                    document.getElementById('appointment_type').value = data.appointment.appointment_type;
                } else {
                    alert('Ошибка загрузки данных: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка при загрузке данных');
            });
    }
    
    function editAppointment(id) {
        openModal(id);
    }
    
    function updateStatus(id, status) {
        if (confirm('Вы уверены, что хотите изменить статус записи?')) {
            fetch('update_appointment_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка при обновлении статуса');
            });
        }
    }
    
    // Обработка формы
    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('id', currentAppointmentId);
        
        fetch('save_appointment.php', {
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
    
    // Закрытие модального окна при клике вне его
    window.onclick = function(event) {
        const modal = document.getElementById('appointmentModal');
        if (event.target == modal) {
            closeModal();
        }
    }
    
</script>
<script>
    // Глобальные переменные
    let currentAppointmentId = null;
    let isEditMode = false;
    
    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        loadFilters();
        applyFilters();
        setupRealTimeFilters();
        checkForClientPreselection();
        
        // Устанавливаем минимальную дату для поля даты
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('appointment_date').min = today;
        
        // Настраиваем время по умолчанию
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = Math.ceil(now.getMinutes() / 15) * 15; // Округляем до ближайших 15 минут
        const defaultTime = `${hours}:${minutes.toString().padStart(2, '0')}`;
        document.getElementById('appointment_time').value = defaultTime;
        
        // Добавляем обработчик для автоматического заполнения времени окончания
        setupAutoDuration();
    });
    
    // Проверка предварительного выбора клиента
    function checkForClientPreselection() {
        const clientId = localStorage.getItem('selectedClientId');
        if (clientId) {
            // Даем небольшую задержку чтобы select успел загрузиться
            setTimeout(() => {
                const clientSelect = document.getElementById('client_id');
                if (clientSelect) {
                    clientSelect.value = clientId;
                    // Прокручиваем к выбранному варианту
                    const option = clientSelect.querySelector(`option[value="${clientId}"]`);
                    if (option) {
                        option.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
                localStorage.removeItem('selectedClientId');
            }, 500);
        }
        
        // Проверяем параметр URL для редактирования
        const urlParams = new URLSearchParams(window.location.search);
        const editId = urlParams.get('edit');
        if (editId) {
            openModal(editId);
        }
    }
    
    // Настройка автоматической продолжительности
    function setupAutoDuration() {
        const appointmentTypeSelect = document.getElementById('appointment_type');
        const durationMap = {
            'консультация': 60,
            'замер': 45,
            'примерка': 30,
            'выдача': 30,
            'другое': 30
        };
        
        appointmentTypeSelect.addEventListener('change', function() {
            const duration = durationMap[this.value] || 30;
            // Можно добавить логику для расчета времени окончания
        });
    }
    
    // Настройка фильтров в реальном времени
    function setupRealTimeFilters() {
        let filterTimeout;
        const filterInputs = ['filterDateFrom', 'filterDateTo', 'filterStatus'];
        
        filterInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                input.addEventListener('change', function() {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(applyFilters, 300);
                });
            }
        });
    }
    
    // Открытие модального окна
    function openModal(id = null) {
        const modal = document.getElementById('appointmentModal');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Блокируем скролл страницы
        
        if (id) {
            currentAppointmentId = id;
            isEditMode = true;
            document.getElementById('modalTitle').textContent = 'Редактировать запись на прием';
            document.getElementById('appointmentId').value = id;
            loadAppointmentData(id);
        } else {
            currentAppointmentId = null;
            isEditMode = false;
            document.getElementById('modalTitle').textContent = 'Новая запись на прием';
            document.getElementById('appointmentForm').reset();
            document.getElementById('appointmentId').value = '';
            
            // Устанавливаем сегодняшнюю дату по умолчанию
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('appointment_date').value = today;
            
            // Устанавливаем следующее доступное время (округляем до ближайших 15 минут)
            const now = new Date();
            const minutes = Math.ceil(now.getMinutes() / 15) * 15;
            now.setMinutes(minutes);
            now.setSeconds(0);
            const timeString = now.toTimeString().substring(0, 5);
            document.getElementById('appointment_time').value = timeString;
        }
    }
    
    // Закрытие модального окна
    function closeModal() {
        const modal = document.getElementById('appointmentModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Возвращаем скролл
        currentAppointmentId = null;
        isEditMode = false;
        document.getElementById('appointmentForm').reset();
    }
    
    // Загрузка данных записи
    function loadAppointmentData(id) {
        showLoading('Загрузка данных...');
        fetch(`get_appointment.php?id=${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка сети');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                if (data.success && data.appointment) {
                    const appointment = data.appointment;
                    
                    // Заполняем форму данными
                    document.getElementById('client_id').value = appointment.client_id;
                    document.getElementById('user_id').value = appointment.user_id;
                    document.getElementById('appointment_date').value = appointment.appointment_date;
                    document.getElementById('appointment_time').value = appointment.appointment_time.substring(0, 5); // Берем только часы:минуты
                    document.getElementById('appointment_type').value = appointment.appointment_type;
                    
                    // Прокручиваем к верху модального окна
                    const modalContent = document.querySelector('.modal-content');
                    if (modalContent) {
                        modalContent.scrollTop = 0;
                    }
                    
                    showMessage('success', 'Данные загружены');
                } else {
                    showMessage('error', data.message || 'Ошибка загрузки данных');
                    closeModal();
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showMessage('error', 'Ошибка при загрузке данных: ' + error.message);
                closeModal();
            });
    }
    
    // Редактирование записи
    function editAppointment(id) {
        openModal(id);
    }
    
    // Обновление статуса записи
    function updateStatus(id, status) {
        const statusTexts = {
            'completed': { text: 'завершен', confirm: 'завершить' },
            'cancelled': { text: 'отменен', confirm: 'отменить' },
            'planned': { text: 'запланирован', confirm: 'запланировать' }
        };
        
        const statusInfo = statusTexts[status] || { text: status, confirm: status };
        
        if (confirm(`Вы уверены, что хотите ${statusInfo.confirm} эту запись?`)) {
            showLoading('Обновление статуса...');
            fetch('update_appointment_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}&status=${statusInfo.text}`
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showMessage('success', data.message);
                    // Обновляем строку в таблице
                    if (data.appointment) {
                        updateTableRow(id, data.appointment);
                    } else {
                        // Если нет данных в ответе, перезагружаем таблицу
                        setTimeout(applyFilters, 1000);
                    }
                } else {
                    showMessage('error', data.message);
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showMessage('error', 'Ошибка при обновлении статуса');
            });
        }
    }
    
    // Удаление записи
    function deleteAppointment(id, clientName = '') {
        const clientText = clientName ? ` для клиента "${clientName}"` : '';
        
        if (confirm(`Вы уверены, что хотите удалить запись${clientText}?\n\nЭто действие нельзя отменить.`)) {
            showLoading('Удаление записи...');
            fetch('delete_appointment.php', {
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
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        // Анимация удаления
                        row.style.transition = 'all 0.3s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-100%)';
                        setTimeout(() => row.remove(), 300);
                    }
                    
                    // Если таблица пуста, показываем сообщение
                    setTimeout(() => {
                        const tbody = document.querySelector('table tbody');
                        if (tbody && tbody.children.length === 0) {
                            showEmptyTableMessage();
                        }
                    }, 350);
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
    
    // Дублирование записи
    function duplicateAppointment(id) {
        showLoading('Создание копии...');
        fetch(`get_appointment.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success && data.appointment) {
                    const appointment = data.appointment;
                    
                    // Открываем модальное окно с данными для дублирования
                    openModal();
                    
                    // Заполняем форму теми же данными
                    setTimeout(() => {
                        document.getElementById('client_id').value = appointment.client_id;
                        document.getElementById('user_id').value = appointment.user_id;
                        document.getElementById('appointment_type').value = appointment.appointment_type;
                        
                        // Устанавливаем дату на завтра
                        const tomorrow = new Date();
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        document.getElementById('appointment_date').value = tomorrow.toISOString().split('T')[0];
                        
                        // Сохраняем то же время
                        document.getElementById('appointment_time').value = appointment.appointment_time.substring(0, 5);
                        
                        showMessage('info', 'Заполнены данные для дублирования. Измените дату при необходимости.');
                    }, 100);
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showMessage('error', 'Ошибка при дублировании записи');
            });
    }
    
    // Обновление строки в таблице
    function updateTableRow(id, appointment) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (!row) {
            // Если строка не найдена, обновляем всю таблицу
            applyFilters();
            return;
        }
        
        // Обновляем данные в строке
        row.cells[0].innerHTML = formatDate(appointment.appointment_date);
        row.cells[1].textContent = appointment.appointment_time.substring(0, 5);
        row.cells[2].textContent = appointment.client_name || '—';
        row.cells[3].textContent = appointment.employee_name || '—';
        row.cells[4].textContent = appointment.appointment_type;
        row.cells[5].innerHTML = `<span class="status-${appointment.status}">${appointment.status}</span>`;
        row.cells[6].innerHTML = formatDateTime(appointment.created_at);
        
        // Обновляем кнопки действий в зависимости от статуса
        updateActionButtons(row, appointment);
        
        // Добавляем анимацию обновления
        row.style.backgroundColor = '#e8f5e8';
        setTimeout(() => {
            row.style.backgroundColor = '';
        }, 1000);
    }
    
    // Обновление кнопок действий в строке
    function updateActionButtons(row, appointment) {
        const actionCell = row.cells[7];
        const status = appointment.status;
        
        let buttonsHtml = `
            <div class="action-buttons">
                <button class="btn btn-sm" onclick="editAppointment(${appointment.id})" title="Редактировать">
                    <span class="btn-icon">✏️</span>
                </button>
                <button class="btn btn-sm btn-info" onclick="duplicateAppointment(${appointment.id})" title="Дублировать">
                    <span class="btn-icon">📋</span>
                </button>
        `;
        
        if (status === 'запланирован') {
            buttonsHtml += `
                <button class="btn btn-sm btn-success" onclick="updateStatus(${appointment.id}, 'completed')" title="Завершить">
                    <span class="btn-icon">✓</span>
                </button>
                <button class="btn btn-sm btn-warning" onclick="updateStatus(${appointment.id}, 'cancelled')" title="Отменить">
                    <span class="btn-icon">✕</span>
                </button>
            `;
        } else if (status === 'отменен' || status === 'завершен') {
            buttonsHtml += `
                <button class="btn btn-sm btn-secondary" onclick="updateStatus(${appointment.id}, 'planned')" title="Вернуть в запланированные">
                    <span class="btn-icon">↶</span>
                </button>
            `;
        }
        
        buttonsHtml += `
                <button class="btn btn-sm btn-danger" onclick="deleteAppointment(${appointment.id}, '${escapeHtml(appointment.client_name)}')" title="Удалить">
                    <span class="btn-icon">🗑️</span>
                </button>
            </div>
        `;
        
        actionCell.innerHTML = buttonsHtml;
    }
    
    // Применение фильтров
    function applyFilters() {
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;
        const status = document.getElementById('filterStatus').value;
        const client = document.getElementById('filterClient').value;
        const employee = document.getElementById('filterEmployee').value;
        
        const params = new URLSearchParams();
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        if (status) params.append('status', status);
        if (client) params.append('client_id', client);
        if (employee) params.append('employee_id', employee);
        
        showLoading('Применение фильтров...');
        fetch(`get_appointments.php?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    updateAppointmentsTable(data.appointments);
                    saveFilters();
                    showMessage('success', `Найдено записей: ${data.appointments.length}`);
                } else {
                    showMessage('error', data.message || 'Ошибка при загрузке данных');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showMessage('error', 'Ошибка при загрузке данных');
            });
    }
    
    // Очистка фильтров
    function clearFilters() {
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterClient').value = '';
        document.getElementById('filterEmployee').value = '';
        
        localStorage.removeItem('appointmentFilters');
        applyFilters();
        
        showMessage('info', 'Фильтры очищены');
    }
    
    // Экспорт данных в CSV
    function exportToCSV() {
        showLoading('Подготовка экспорта...');
        fetch('export_appointments.php')
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success && data.csv) {
                    // Создаем ссылку для скачивания
                    const blob = new Blob([data.csv], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    const url = URL.createObjectURL(blob);
                    
                    link.setAttribute('href', url);
                    link.setAttribute('download', `appointments_${new Date().toISOString().split('T')[0]}.csv`);
                    link.style.visibility = 'hidden';
                    
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    showMessage('success', 'Экспорт завершен успешно');
                } else {
                    showMessage('error', data.message || 'Ошибка при экспорте');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showMessage('error', 'Ошибка при экспорте');
            });
    }
    
    // Сохранение фильтров в localStorage
    function saveFilters() {
        const filters = {
            dateFrom: document.getElementById('filterDateFrom').value,
            dateTo: document.getElementById('filterDateTo').value,
            status: document.getElementById('filterStatus').value,
            client: document.getElementById('filterClient').value,
            employee: document.getElementById('filterEmployee').value
        };
        localStorage.setItem('appointmentFilters', JSON.stringify(filters));
    }
    
    // Загрузка фильтров из localStorage
    function loadFilters() {
        const savedFilters = localStorage.getItem('appointmentFilters');
        if (savedFilters) {
            try {
                const filters = JSON.parse(savedFilters);
                document.getElementById('filterDateFrom').value = filters.dateFrom || '';
                document.getElementById('filterDateTo').value = filters.dateTo || '';
                document.getElementById('filterStatus').value = filters.status || '';
                document.getElementById('filterClient').value = filters.client || '';
                document.getElementById('filterEmployee').value = filters.employee || '';
            } catch (e) {
                console.error('Error parsing saved filters:', e);
            }
        }
    }
    
    // Обновление таблицы записей
    function updateAppointmentsTable(appointments) {
        const tbody = document.querySelector('table tbody');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        if (appointments.length === 0) {
            showEmptyTableMessage();
            return;
        }
        
        // Сортируем записи по дате и времени
        appointments.sort((a, b) => {
            const dateA = new Date(`${a.appointment_date}T${a.appointment_time}`);
            const dateB = new Date(`${b.appointment_date}T${b.appointment_time}`);
            return dateB - dateA; // Сначала новые
        });
        
        appointments.forEach(appointment => {
            const row = document.createElement('tr');
            row.dataset.id = appointment.id;
            row.dataset.status = appointment.status;
            
            // Определяем класс для строки в зависимости от статуса
            if (appointment.status === 'завершен') {
                row.classList.add('row-completed');
            } else if (appointment.status === 'отменен') {
                row.classList.add('row-cancelled');
            } else if (new Date(appointment.appointment_date) < new Date()) {
                row.classList.add('row-overdue');
            }
            
            const statusClass = appointment.status === 'запланирован' ? 'status-planned' :
                               appointment.status === 'завершен' ? 'status-completed' :
                               appointment.status === 'отменен' ? 'status-cancelled' : 'status-default';
            
            row.innerHTML = `
                <td>
                    <div class="date-cell">
                        <div class="date-day">${formatDate(appointment.appointment_date)}</div>
                        <div class="date-weekday">${getWeekday(appointment.appointment_date)}</div>
                    </div>
                </td>
                <td>
                    <div class="time-cell">${appointment.appointment_time.substring(0, 5)}</div>
                </td>
                <td>
                    <div class="client-cell">
                        <div class="client-name">${escapeHtml(appointment.client_name || '—')}</div>
                        ${appointment.client_phone ? `<div class="client-phone">${escapeHtml(appointment.client_phone)}</div>` : ''}
                    </div>
                </td>
                <td>
                    <div class="employee-cell">
                        <div class="employee-name">${escapeHtml(appointment.employee_name || '—')}</div>
                        ${appointment.employee_phone ? `<div class="employee-phone">${escapeHtml(appointment.employee_phone)}</div>` : ''}
                    </div>
                </td>
                <td>
                    <span class="appointment-type ${appointment.appointment_type.toLowerCase()}">
                        ${escapeHtml(appointment.appointment_type)}
                    </span>
                </td>
                <td>
                    <span class="status-badge ${statusClass}">
                        ${appointment.status}
                    </span>
                </td>
                <td>
                    <div class="created-cell">
                        ${formatDateTime(appointment.created_at)}
                    </div>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-edit" onclick="editAppointment(${appointment.id})" title="Редактировать">
                            <span class="btn-icon">✏️</span>
                        </button>
                        <button class="btn btn-sm btn-duplicate" onclick="duplicateAppointment(${appointment.id})" title="Дублировать">
                            <span class="btn-icon">📋</span>
                        </button>
                        ${appointment.status === 'запланирован' ? `
                            <button class="btn btn-sm btn-complete" onclick="updateStatus(${appointment.id}, 'completed')" title="Завершить">
                                <span class="btn-icon">✓</span>
                            </button>
                            <button class="btn btn-sm btn-cancel" onclick="updateStatus(${appointment.id}, 'cancelled')" title="Отменить">
                                <span class="btn-icon">✕</span>
                            </button>
                        ` : ''}
                        ${appointment.status === 'отменен' || appointment.status === 'завершен' ? `
                            <button class="btn btn-sm btn-restore" onclick="updateStatus(${appointment.id}, 'planned')" title="Вернуть в запланированные">
                                <span class="btn-icon">↶</span>
                            </button>
                        ` : ''}
                        <button class="btn btn-sm btn-delete" onclick="deleteAppointment(${appointment.id}, '${escapeHtml(appointment.client_name)}')" title="Удалить">
                            <span class="btn-icon">🗑️</span>
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
        if (!tbody) return;
        
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 60px 20px;">
                    <div style="max-width: 400px; margin: 0 auto;">
                        <div style="font-size: 64px; margin-bottom: 20px; opacity: 0.3;">📅</div>
                        <div style="font-size: 20px; color: #666; margin-bottom: 10px; font-weight: 500;">
                            Записи не найдены
                        </div>
                        <div style="color: #999; margin-bottom: 30px; line-height: 1.5;">
                            Попробуйте изменить параметры фильтрации<br>или добавьте новую запись на прием
                        </div>
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <button class="btn btn-accent" onclick="openModal()">
                                + Добавить первую запись
                            </button>
                            <button class="btn" onclick="clearFilters()">
                                🗑️ Очистить фильтры
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    }
    
    // Обработка формы записи
    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Валидация формы
        if (!validateAppointmentForm()) {
            return;
        }
        
        const formData = new FormData(this);
        if (currentAppointmentId) {
            formData.append('id', currentAppointmentId);
        }
        
        // Показываем индикатор загрузки
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Сохранение...';
        submitBtn.disabled = true;
        
        showLoading(isEditMode ? 'Обновление записи...' : 'Создание записи...');
        
        fetch('save_appointment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showMessage('success', data.message);
                closeModal();
                setTimeout(() => {
                    applyFilters(); // Обновляем таблицу
                }, 500);
            } else {
                showMessage('error', data.message);
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showMessage('error', 'Ошибка при сохранении: ' + error.message);
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Валидация формы записи
    function validateAppointmentForm() {
        const clientId = document.getElementById('client_id').value;
        const userId = document.getElementById('user_id').value;
        const appointmentDate = document.getElementById('appointment_date').value;
        const appointmentTime = document.getElementById('appointment_time').value;
        const appointmentType = document.getElementById('appointment_type').value;
        
        const errors = [];
        
        if (!clientId) errors.push('Выберите клиента');
        if (!userId) errors.push('Выберите сотрудника');
        if (!appointmentDate) errors.push('Укажите дату приема');
        if (!appointmentTime) errors.push('Укажите время приема');
        if (!appointmentType) errors.push('Выберите тип приема');
        
        // Проверка на прошедшую дату
        if (appointmentDate) {
            const selectedDate = new Date(appointmentDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                errors.push('Нельзя создавать записи на прошедшие даты');
            }
        }
        
        // Проверка на пересечение времени
        // (здесь можно добавить проверку на занятость времени)
        
        if (errors.length > 0) {
            showMessage('error', 'Исправьте ошибки:<br>' + errors.join('<br>'));
            return false;
        }
        
        return true;
    }
    
    // Вспомогательные функции
    function formatDate(dateString) {
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } catch (e) {
            return dateString;
        }
    }
    
    function formatDateTime(datetimeString) {
        try {
            const date = new Date(datetimeString);
            return date.toLocaleString('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return datetimeString;
        }
    }
    
    function getWeekday(dateString) {
        const weekdays = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
        try {
            const date = new Date(dateString);
            return weekdays[date.getDay()];
        } catch (e) {
            return '';
        }
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function showMessage(type, text) {
        // Удаляем старые сообщения
        const oldMessages = document.querySelectorAll('.message-overlay');
        oldMessages.forEach(msg => msg.remove());
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message-overlay message-${type}`;
        messageDiv.innerHTML = `
            <div class="message-content">
                <div class="message-icon">${type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ'}</div>
                <div class="message-text">${text}</div>
                <button class="message-close" onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        `;
        
        messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            animation: messageSlideIn 0.3s ease;
        `;
        
        document.body.appendChild(messageDiv);
        
        // Автоматическое скрытие через 5 секунд
        setTimeout(() => {
            if (messageDiv.parentElement) {
                messageDiv.style.animation = 'messageSlideOut 0.3s ease';
                setTimeout(() => messageDiv.remove(), 300);
            }
        }, 5000);
    }
    
    function showLoading(text = 'Загрузка...') {
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
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(2px);
                z-index: 9999;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            `;
            loadingDiv.innerHTML = `
                <div class="loading-spinner"></div>
                <div class="loading-text" style="margin-top: 20px; color: #666; font-size: 16px;">${text}</div>
            `;
            document.body.appendChild(loadingDiv);
        } else {
            loadingDiv.style.display = 'flex';
            const loadingText = loadingDiv.querySelector('.loading-text');
            if (loadingText) loadingText.textContent = text;
        }
    }
    
    function hideLoading() {
        const loadingDiv = document.getElementById('loadingOverlay');
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }
    }
    
<script>

</body>
</html>