<?php
session_start();
require_once 'config.php';
checkAuth();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управления - Ортопедический салон</title>
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
        
        .nav-link i {
            margin-right: 10px;
            font-size: 18px;
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
        
        .logout-btn:hover {
            background-color: #e05a00;
        }
        
        .content {
            padding: 30px;
            flex: 1;
        }
        
        .welcome {
            background-color: var(--white);
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .welcome h2 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--dark-gray);
            font-size: 14px;
        }
        
        .recent-activity {
            background-color: var(--white);
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .section-title {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-gray);
        }
        
        .activity-list {
            list-style: none;
        }
        
        .activity-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-info {
            flex: 1;
        }
        
        .activity-time {
            color: #666;
            font-size: 12px;
        }
        
        .btn {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 8px 15px;
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
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <i>📊</i> Панель управления
                </a>
            </li>
            <li class="nav-item">
                <a href="appointments.php" class="nav-link">
                    <i>📅</i> Записи на прием
                </a>
            </li>
            <li class="nav-item">
                <a href="clients.php" class="nav-link">
                    <i>👥</i> Клиенты
                </a>
            </li>
            <li class="nav-item">
                <a href="orders.php" class="nav-link">
                    <i>📋</i> Заказы
                </a>
            </li>
            <li class="nav-item">
                <a href="schedule.php" class="nav-link">
                    <i>⏰</i> График работы
                </a>
            </li>
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <li class="nav-item">
                <a href="employees.php" class="nav-link">
                    <i>⚙️</i> Сотрудники
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    
    <!-- Основной контент -->
    <div class="main-content">
        <div class="header">
            <h2>Панель управления</h2>
            <div class="user-info">
                <div class="user-name">
                    <?php echo htmlspecialchars($_SESSION['full_name']); ?> 
                    (<?php echo $_SESSION['role'] == 'admin' ? 'Администратор' : 'Сотрудник'; ?>)
                </div>
                <a href="logout.php" class="logout-btn">Выйти</a>
            </div>
        </div>
        
        <div class="content">
            <div class="welcome">
                <h2>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
                <p>Сегодня <?php echo date('d.m.Y'); ?>. У вас запланировано мероприятий: <strong>3</strong></p>
            </div>
            
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Записей на сегодня</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Активных заказов</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">24</div>
                    <div class="stat-label">Новых клиентов</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">5</div>
                    <div class="stat-label">Завершенных заказов</div>
                </div>
            </div>
            
            <div class="recent-activity">
                <h3 class="section-title">Ближайшие записи</h3>
                <ul class="activity-list">
                    <li class="activity-item">
                        <div class="activity-info">
                            <strong>Смирнов А.В.</strong> - Консультация
                            <div class="activity-time">Сегодня, 10:00</div>
                        </div>
                        <a href="#" class="btn">Подробнее</a>
                    </li>
                    <li class="activity-item">
                        <div class="activity-info">
                            <strong>Козлова А.С.</strong> - Примерка
                            <div class="activity-time">Сегодня, 14:30</div>
                        </div>
                        <a href="#" class="btn">Подробнее</a>
                    </li>
                    <li class="activity-item">
                        <div class="activity-info">
                            <strong>Волков Д.П.</strong> - Выдача заказа
                            <div class="activity-time">Завтра, 11:15</div>
                        </div>
                        <a href="#" class="btn">Подробнее</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardStats();
        loadUpcomingAppointments();
        
        // Обновляем статистику каждые 5 минут
        setInterval(loadDashboardStats, 300000);
    });
    
    function loadDashboardStats() {
        fetch('get_dashboard_stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStatsCards(data.stats);
                }
            })
            .catch(error => {
                console.error('Error loading stats:', error);
            });
    }
    
    function updateStatsCards(stats) {
        // Обновляем карточки статистики
        document.querySelectorAll('.stat-card').forEach(card => {
            const statType = card.dataset.stat;
            if (stats[statType] !== undefined) {
                const numberElement = card.querySelector('.stat-number');
                if (numberElement) {
                    numberElement.textContent = stats[statType];
                }
            }
        });
    }
    
    function loadUpcomingAppointments() {
        fetch('get_upcoming_appointments.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateAppointmentsList(data.appointments);
                }
            })
            .catch(error => {
                console.error('Error loading appointments:', error);
            });
    }
    
    function updateAppointmentsList(appointments) {
        const list = document.querySelector('.activity-list');
        if (!list) return;
        
        list.innerHTML = '';
        
        if (appointments.length === 0) {
            list.innerHTML = `
                <li class="activity-item">
                    <div class="activity-info">
                        <div style="color: #666; text-align: center; padding: 20px;">
                            На сегодня нет запланированных приемов
                        </div>
                    </div>
                </li>
            `;
            return;
        }
        
        appointments.forEach(appointment => {
            const item = document.createElement('li');
            item.className = 'activity-item';
            
            const time = appointment.appointment_time.substring(0, 5);
            const date = new Date(appointment.appointment_date).toLocaleDateString('ru-RU');
            
            item.innerHTML = `
                <div class="activity-info">
                    <strong>${escapeHtml(appointment.client_name)}</strong> - ${escapeHtml(appointment.appointment_type)}
                    <div class="activity-time">${date}, ${time}</div>
                </div>
                <a href="appointments.php?edit=${appointment.id}" class="btn">Подробнее</a>
            `;
            
            list.appendChild(item);
        });
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
</body>
</html>