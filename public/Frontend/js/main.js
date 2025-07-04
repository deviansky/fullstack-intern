document.addEventListener('DOMContentLoaded', () => {

    // Variabel global untuk autentikasi
    const token = localStorage.getItem('authToken');
    const userData = JSON.parse(localStorage.getItem('userData'));
    const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    };

    // =================================================================
    // LOGIKA HANYA UNTUK HALAMAN LOGIN (index.html)
    // =================================================================
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorMessage = document.getElementById('error-message');
            errorMessage.classList.add('d-none');
            const loginData = { email: document.getElementById('email').value, password: document.getElementById('password').value };

            try {
                const response = await fetch('/api/login', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(loginData) });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Login gagal!');
                
                localStorage.setItem('authToken', data.access_token);
                localStorage.setItem('userData', JSON.stringify(data.user));
                window.location.href = 'dashboard.html';
            } catch (error) {
                errorMessage.textContent = error.message;
                errorMessage.classList.remove('d-none');
            }
        });
    }

    // =================================================================
    // LOGIKA HANYA UNTUK HALAMAN DASHBOARD (dashboard.html)
    // =================================================================
    const dashboardContainer = document.getElementById('dashboard-container');
    if (dashboardContainer) {
        if (!token) { window.location.href = 'index.html'; return; }

        let allTasks = [];
        let userMap = new Map();

        const views = {
            tasks: document.getElementById('tasks-view'),
            users: document.getElementById('users-view'),
            logs: document.getElementById('logs-view'),
        };
        const navLinks = {
            tasks: document.getElementById('nav-tasks'),
            users: document.getElementById('nav-users'),
            logs: document.getElementById('nav-logs'),
        };
        const pageTitle = document.getElementById('page-title');
        const taskListContainer = document.getElementById('task-list');
        const editTaskForm = document.getElementById('edit-task-form');

        function showView(viewName) {
            Object.values(views).forEach(view => view && view.classList.remove('active'));
            Object.values(navLinks).forEach(link => link && link.classList.remove('active'));
            if (views[viewName] && navLinks[viewName]) {
                views[viewName].classList.add('active');
                navLinks[viewName].classList.add('active');
                pageTitle.textContent = navLinks[viewName].textContent;
            }
        }

        function renderStats(tasks) {
            const statsContainer = document.getElementById('stats-cards');
            if(!statsContainer) return;
            const pendingTasks = tasks.filter(t => t.status === 'pending').length;
            const progressTasks = tasks.filter(t => t.status === 'in progress').length;
            const doneTasks = tasks.filter(t => t.status === 'done').length;
            statsContainer.innerHTML = `
            <div class="col-md-4">
                <div class="card text-center text-bg-secondary mb-3">
                    <div class="card-body">
                        <h2 class="card-title">${pendingTasks}</h2>
                        <p class="card-text">Tugas Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center text-bg-warning mb-3">
                    <div class="card-body">
                        <h2 class="card-title">${progressTasks}</h2>
                        <p class="card-text">Tugas Proggres</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4"><div class="card text-center text-bg-success mb-3"><div class="card-body"><h2 class="card-title">${doneTasks}</h2><p class="card-text">Tugas Selesai</p></div></div></div>`;
        }

        function renderTasks(tasks) {
            if (!taskListContainer) return;
            taskListContainer.innerHTML = '';
            if (tasks.length === 0) { taskListContainer.innerHTML = '<p class="col-12">Tidak ada tugas untuk ditampilkan.</p>'; return; }

            tasks.forEach(task => {
                const assignedUser = task.assigned_to || { name: 'N/A' };
                const statusInfo = { 'pending': { class: 'text-bg-secondary', text: 'Pending' }, 'in progress': { class: 'text-bg-primary', text: 'In Progress' }, 'done': { class: 'text-bg-success', text: 'Done' }};
                const currentStatus = statusInfo[task.status] || { class: 'text-bg-dark', text: 'Unknown' };
                let buttons = '';
                const canUpdate = userData.role === 'admin' || userData.id === task.created_by || userData.id === task.assigned_to;
                const canDelete = userData.role === 'admin' || userData.id === task.created_by;
                if (canUpdate) buttons += `<button class="btn btn-sm btn-warning me-2 edit-btn" data-task-id="${task.id}">Edit</button>`;
                if (canDelete) buttons += `<button class="btn btn-sm btn-danger delete-btn" data-task-id="${task.id}">Delete</button>`;
                const cardWrapper = document.createElement('div');
                cardWrapper.className = 'col-md-6 col-lg-4 mb-4';
                cardWrapper.innerHTML = `
                    <div class="card h-100">
                        <div class="card-body pb-2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0 me-2">${task.title}</h5>
                                <span class="badge ${currentStatus.class} flex-shrink-0">${currentStatus.text}</span>
                            </div>
                            <p class="card-text small text-muted">${task.description}</p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-muted">
                                    <span>To: ${assignedUser.name}</span><br>
                                    <span>Due: ${new Date(task.due_date).toLocaleDateString('id-ID')}</span>
                                </div>
                                <div class="d-flex gap-2">${buttons}</div>
                            </div>
                        </div>
                    </div>`;
                taskListContainer.appendChild(cardWrapper);
            });
        }

        async function fetchAndRenderTasks() {
            showView('tasks');
            if (taskListContainer) taskListContainer.innerHTML = '<p class="col-12">Memuat data...</p>';
            try {
                const [tasksResponse, usersResponse] = await Promise.all([ fetch('/api/tasks', { headers }), fetch('/api/users', { headers }) ]);
                if (!tasksResponse.ok || !usersResponse.ok) throw new Error('Gagal memuat data awal.');
                
                allTasks = await tasksResponse.json();
                const users = await usersResponse.json();
                userMap = new Map(users.map(user => [user.id, user]));

                renderStats(allTasks);
                renderTasks(allTasks);
            } catch (error) {
                if (taskListContainer) taskListContainer.innerHTML = `<p class="col-12 text-danger">${error.message}</p>`;
            }
        }

        async function showUsersView() {
            showView('users');
            const userListTable = document.getElementById('user-list-table');
            if(!userListTable) return;
            userListTable.innerHTML = '<tr><td colspan="4">Memuat...</td></tr>';
            try {
                const response = await fetch('/api/users', { headers });
                if (!response.ok) throw new Error('Gagal memuat pengguna.');
                const users = await response.json();
                userListTable.innerHTML = '';
                users.forEach(user => {
                    const row = userListTable.insertRow();
                    row.innerHTML = `<td>${user.name}</td><td>${user.email}</td><td><span class="badge bg-info">${user.role}</span></td><td><span class="badge ${user.status ? 'bg-success' : 'bg-danger'}">${user.status ? 'Aktif' : 'Nonaktif'}</span></td>`;
                });
            } catch (error) {
                userListTable.innerHTML = `<tr><td colspan="4" class="text-danger">${error.message}</td></tr>`;
            }
        }

        async function showLogsView() {
            showView('logs');
            const logList = document.getElementById('log-list');
            if(!logList) return;
            logList.innerHTML = 'Memuat log...';
            try {
                const response = await fetch('/api/logs', { headers });
                if (!response.ok) throw new Error('Gagal memuat log.');
                const logs = await response.json();
                logList.innerHTML = '';
                logs.forEach(log => {
                    const logEntry = document.createElement('div');
                    logEntry.className = 'alert alert-secondary';
                    logEntry.textContent = `[${new Date(log.logged_at).toLocaleString()}] [${log.action}] - ${log.description}`;
                    logList.appendChild(logEntry);
                });
            } catch(error) {
                logList.innerHTML = `<p class="text-danger">${error.message}</p>`;
            }
        }

        // --- Inisialisasi & Event Listeners ---
        document.getElementById('user-info').textContent = `${userData.name} (${userData.role})`;
        if (userData.role === 'admin') {
            document.getElementById('nav-users-li').classList.remove('d-none');
            document.getElementById('nav-logs-li').classList.remove('d-none');
        }

        navLinks.tasks.addEventListener('click', (e) => { e.preventDefault(); fetchAndRenderTasks(); });
        if(userData.role === 'admin') {
            navLinks.users.addEventListener('click', (e) => { e.preventDefault(); showUsersView(); });
            navLinks.logs.addEventListener('click', (e) => { e.preventDefault(); showLogsView(); });
        }

        document.getElementById('logout-button').addEventListener('click', () => { localStorage.clear(); window.location.href = 'index.html'; });

        taskListContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-btn')) {
                const taskId = e.target.dataset.taskId;
                if (confirm('Yakin ingin menghapus tugas ini?')) {
                    fetch(`/api/tasks/${taskId}`, { method: 'DELETE', headers })
                        .then(response => { if (!response.ok) throw new Error('Gagal menghapus tugas.'); alert('Tugas berhasil dihapus.'); fetchAndRenderTasks(); })
                        .catch(error => alert(error.message));
                }
            }
            if (e.target.classList.contains('edit-btn')) {
                const taskId = e.target.dataset.taskId;
                const taskToEdit = allTasks.find(t => t.id === taskId);
                if (taskToEdit) {
                    document.getElementById('edit-task-id').value = taskToEdit.id;
                    document.getElementById('edit-task-title').value = taskToEdit.title;
                    document.getElementById('edit-task-description').value = taskToEdit.description;
                    document.getElementById('edit-task-status').value = taskToEdit.status;
                    const editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                    editModal.show();
                }
            }
        });

        editTaskForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const taskId = document.getElementById('edit-task-id').value;
            const updatedData = { title: document.getElementById('edit-task-title').value, description: document.getElementById('edit-task-description').value, status: document.getElementById('edit-task-status').value };
            try {
                const response = await fetch(`/api/tasks/${taskId}`, { method: 'PUT', headers, body: JSON.stringify(updatedData) });
                if (!response.ok) throw new Error('Gagal mengupdate tugas.');
                alert('Tugas berhasil diperbarui.');
                const modal = bootstrap.Modal.getInstance(document.getElementById('editTaskModal'));
                if (modal) modal.hide();
                fetchAndRenderTasks();
            } catch (error) {
                alert(error.message);
            }
        });

        fetchAndRenderTasks();
    }
    
    // =================================================================
    // LOGIKA HANYA UNTUK HALAMAN CREATE TASK
    // =================================================================
    const createTaskForm = document.getElementById('create-task-form');
    if (createTaskForm) {
        if (!token) { window.location.href = 'index.html'; return; }
        const assignSelect = document.getElementById('new-task-assign-to');
        fetch('/api/users', { headers }).then(res => res.json()).then(users => {
            assignSelect.innerHTML = '';
            users.forEach(user => { const option = document.createElement('option'); option.value = user.id; option.textContent = `${user.name} (${user.role})`; assignSelect.appendChild(option); });
        }).catch(err => console.error("Gagal memuat user untuk dropdown", err));
        
        createTaskForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const taskData = { title: document.getElementById('new-task-title').value, description: document.getElementById('new-task-description').value, assigned_to: document.getElementById('new-task-assign-to').value, due_date: document.getElementById('new-task-due-date').value };
            try {
                const response = await fetch('/api/tasks', { method: 'POST', headers, body: JSON.stringify(taskData) });
                if (!response.ok) { const err = await response.json(); throw new Error(err.message || 'Gagal menyimpan.'); }
                alert('Task berhasil dibuat!');
                window.location.href = 'dashboard.html';
            } catch (error) {
                alert(error.message);
            }
        });
    }

    // =================================================================
    // LOGIKA HANYA UNTUK HALAMAN CREATE USER
    // =================================================================
    const createUserForm = document.getElementById('create-user-form');
    if (createUserForm) {
        if (!token || userData.role !== 'admin') { window.location.href = 'index.html'; return; }
        
        createUserForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const newUserData = { name: document.getElementById('new-user-name').value, email: document.getElementById('new-user-email').value, password: document.getElementById('new-user-password').value, role: document.getElementById('new-user-role').value, status: true };
            try {
                const response = await fetch('/api/users', { method: 'POST', headers, body: JSON.stringify(newUserData) });
                if (!response.ok) { const err = await response.json(); throw new Error(err.message || 'Gagal membuat user.'); }
                alert('User baru berhasil dibuat!');
                window.location.href = 'dashboard.html';
            } catch (error) {
                alert(error.message);
            }
        });
    }
});