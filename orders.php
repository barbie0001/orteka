<?php
session_start();
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();
$orders = $pdo->query("
    SELECT o.*, c.full_name as client_name, u.full_name as employee_name 
    FROM orders o 
    LEFT JOIN clients c ON o.client_id = c.id 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
")->fetchAll();

$clients = $pdo->query("SELECT id, full_name FROM clients ORDER BY full_name")->fetchAll();
$employees = $pdo->query("SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы - Ортопедический салон</title>
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

        .status-new {
            background-color: #e3f2fd;
            color: #1565c0;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .status-inwork {
            background-color: #fff3e0;
            color: #ef6c00;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .status-ready {
            background-color: #e8f5e8;
            color: #2e7d32;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .status-completed {
            background-color: #f3e5f5;
            color: #7b1fa2;
            padding: 4px 8px;
            border-radius: 4px;
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

        .amount {
            font-weight: bold;
            color: var(--primary-color);
        }

        .payment-status {
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .paid {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .partial {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .unpaid {
            background-color: #ffebee;
            color: #c62828;
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
            <li class="nav-item"><a href="clients.php" class="nav-link"><i>👥</i> Клиенты</a></li>
            <li class="nav-item"><a href="orders.php" class="nav-link active"><i>📋</i> Заказы</a></li>
            <li class="nav-item"><a href="schedule.php" class="nav-link"><i>⏰</i> График работы</a></li>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li class="nav-item"><a href="employees.php" class="nav-link"><i>⚙️</i> Сотрудники</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Основной контент -->
    <div class="main-content">
        <div class="header">
            <h2>Управление заказами</h2>
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
                <h2>Список заказов</h2>
                <button class="btn btn-accent" onclick="openModal()">+ Новый заказ</button>
            </div>

            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($orders); ?></div>
                    <div class="stat-label">Всего заказов</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo count(array_filter($orders, fn($o) => $o['status'] == 'новый')); ?></div>
                    <div class="stat-label">Новых заказов</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo count(array_filter($orders, fn($o) => $o['status'] == 'в работе')); ?></div>
                    <div class="stat-label">В работе</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo count(array_filter($orders, fn($o) => $o['status'] == 'готов')); ?></div>
                    <div class="stat-label">Готово к выдаче</div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>Клиент</th>
                            <th>Тип заказа</th>
                            <th>Статус</th>
                            <th>Сумма</th>
                            <th>Оплачено</th>
                            <th>Срок выполнения</th>
                            <th>Ответственный</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order):
                            $payment_status = '';
                            if ($order['paid_amount'] == 0) {
                                $payment_status = 'unpaid';
                            } elseif ($order['paid_amount'] < $order['total_amount']) {
                                $payment_status = 'partial';
                            } else {
                                $payment_status = 'paid';
                            }
                            ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['client_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_type']); ?></td>
                                <td>
                                    <span class="status-<?php
                                    echo $order['status'] == 'новый' ? 'new' :
                                        ($order['status'] == 'в работе' ? 'inwork' :
                                            ($order['status'] == 'готов' ? 'ready' : 'completed'));
                                    ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td class="amount"><?php echo number_format($order['total_amount'], 0, ',', ' '); ?> ₽</td>
                                <td>
                                    <span class="payment-status <?php echo $payment_status; ?>">
                                        <?php echo number_format($order['paid_amount'], 0, ',', ' '); ?> ₽
                                    </span>
                                </td>
                                <td><?php echo $order['completion_date'] ? date('d.m.Y', strtotime($order['completion_date'])) : '—'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($order['employee_name']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm"
                                            onclick="editOrder(<?php echo $order['id']; ?>)">Редактировать</button>
                                        <button class="btn btn-sm btn-accent"
                                            onclick="viewOrder(<?php echo $order['id']; ?>)">Подробнее</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Модальное окно добавления заказа -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Новый заказ</h3>
            <form id="orderForm" method="POST" action="save_order.php">
                <input type="hidden" id="orderId" name="id">

                <div class="form-group">
                    <label for="client_id">Клиент *</label>
                    <select id="client_id" name="client_id" required>
                        <option value="">Выберите клиента</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['id']; ?>">
                                <?php echo htmlspecialchars($client['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="user_id">Ответственный сотрудник *</label>
                    <select id="user_id" name="user_id" required>
                        <option value="">Выберите сотрудника</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?php echo $employee['id']; ?>">
                                <?php echo htmlspecialchars($employee['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="order_type">Тип заказа *</label>
                    <select id="order_type" name="order_type" required>
                        <option value="ортопедическая обувь">Ортопедическая обувь</option>
                        <option value="корсет">Корсет</option>
                        <option value="стельки">Стельки</option>
                        <option value="протез">Протез</option>
                        <option value="другое">Другое</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Описание заказа</label>
                    <textarea id="description" name="description" rows="3"
                        placeholder="Подробное описание заказа..."></textarea>
                </div>

                <div class="form-group">
                    <label for="total_amount">Общая сумма (₽)</label>
                    <input type="number" id="total_amount" name="total_amount" step="0.01" min="0">
                </div>

                <div class="form-group">
                    <label for="paid_amount">Оплаченная сумма (₽)</label>
                    <input type="number" id="paid_amount" name="paid_amount" step="0.01" min="0" value="0">
                </div>

                <div class="form-group">
                    <label for="completion_date">Срок выполнения</label>
                    <input type="date" id="completion_date" name="completion_date">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Отмена</button>
                    <button type="submit" class="btn btn-accent">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('orderModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Новый заказ';
            document.getElementById('orderForm').reset();
            document.getElementById('orderId').value = '';
        }

        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
        }

        function editOrder(id) {
            // Здесь будет AJAX запрос для получения данных заказа
            alert('Редактирование заказа с ID: ' + id);
        }

        function viewOrder(id) {
            // Переход к детальному просмотру заказа
            window.location.href = 'order_details.php?id=' + id;
        }

        // Закрытие модального окна при клике вне его
        window.onclick = function (event) {
            const modal = document.getElementById('orderModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>

</html>