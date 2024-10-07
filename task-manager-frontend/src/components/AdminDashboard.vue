<template>
  <div>
    <div v-if="loading">Loading...</div>
    <div v-if="error" class="error">{{ error }}</div>
    <div class="header">
      <h1>Admin Dashboard</h1>
      <button class="logout" @click="logout">Logout</button>
    </div>
    <h2>Users</h2>
    <table v-if="users.length > 0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Role</th>
          <th>Name</th>
          <th>Email</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.role }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>
            <button @click="viewUserTasks(user.id)">View Tasks</button>
            <button @click="editUser(user)">Edit User</button>
            <button @click="deleteUser(user.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-else>No users found.</div>

    <h2>User Tasks</h2>
    <div v-if="selectedUserTasks.length > 0">
      <h3>Tasks for {{ selectedUser.name }}</h3>
      <table>
        <thead>
          <tr>
            <th>Task ID</th>
            <th>Task Title</th>
            <th>Task Description</th>
            <th>Status</th>
            <th>Due Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="task in selectedUserTasks" :key="task.id">
            <td>{{ task.id }}</td>
            <td>{{ task.title }}</td>
            <td>{{ task.description }}</td>
            <td>{{ task.status }}</td>
            <td>{{ task.due_date }}</td>
            <td>
              <button @click="setEditedTask(task)">Edit Task</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="editedTask.id" class="add-task-card">
      <h2>Edit Task</h2>
      <form @submit.prevent="submitEditTask(selectedUser.id)" class="add-task-form">
        <input v-model="editedTask.title" placeholder="Task Title" required />
        <textarea v-model="editedTask.description" placeholder="Task Description"></textarea>
        <select v-model="editedTask.status" required>
          <option value="Pending">Pending</option>
          <option value="In-Progress">In-Progress</option>
          <option value="Completed">Completed</option>
          <option value="Rejected">Rejected</option>
        </select>
        <input v-model="editedTask.due_date" type="date" required />
        <button type="submit">Update Task</button>
        <button type="button" @click="clearEditedTask">Cancel</button>
      </form>
    </div>

    <div v-if="editingUser.id" class="add-task-card">
      <h2>Edit User</h2>
      <form @submit.prevent="submitEditUser" class="add-task-form">
        <input v-model="editingUser.name" placeholder="User Name" required />
        <input v-model="editingUser.email" type="email" placeholder="User Email" required />
        <select v-model="editingUser.role" required>
          <option value="admin">Admin</option>
          <option value="user">User</option>
        </select>
        <button type="submit">Update User</button>
        <button type="button" @click="clearEditingUser">Cancel</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      users: [],
      loading: true,
      error: null,
      selectedUserTasks: [],
      selectedUser: {},
      editedTask: {
        id: null,
        user_id: null,
        title: '',
        description: '',
        status: '',
        due_date: '',
      },
      editingUser: {
        id: null,
        name: '',
        email: '',
        role: '',
      },
    };
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
    async fetchUsers() {
      try {
        const response = await axios.get('http://localhost:8000/api/admin/users', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });
        this.users = response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred while fetching users.';
      } finally {
        this.loading = false;
      }
    },
    async viewUserTasks(userId) {
      try {
        const response = await axios.get(`http://localhost:8000/api/admin/users/${userId}/tasks`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });
        this.selectedUserTasks = response.data;
        this.selectedUser = this.users.find(user => user.id === userId);
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred while fetching user tasks.';
      }
    },
    setEditedTask(task) {
      this.editedTask = { ...task }; 
    },
    async submitEditTask(userId) {
      const { id, title, description, status, due_date } = this.editedTask;
      if (!id) {
        this.error = "Task ID is undefined. Please select a task to edit.";
        return;
      }

      try {
        const response = await axios.put(`http://localhost:8000/api/admin/users/${userId}/tasks/${id}`, {
          title,
          description,
          status,
          due_date
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });

        const index = this.selectedUserTasks.findIndex(task => task.id === id);
        if (index !== -1) {
          this.selectedUserTasks.splice(index, 1, response.data);
        }
        this.clearEditedTask();
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred while updating the task.';
      }
    },
    clearEditedTask() {
      this.editedTask = { id: null, user_id: null, title: '', description: '', status: '', due_date: '' };
    },
    async deleteUser(userId) {
      if (confirm('Are you sure you want to delete this user?')) {
        try {
          await axios.delete(`http://localhost:8000/api/admin/users/${userId}`, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`
            }
          });
          this.fetchUsers();
        } catch (error) {
          this.error = error.response?.data?.message || 'An error occurred while deleting the user.';
        }
      }
    },
    editUser(user) {
      this.editingUser = { ...user }; 
    },
    async submitEditUser() {
      const { id, name, email, role } = this.editingUser;
      if (!id) {
        this.error = "User ID is undefined. Please select a user to edit.";
        return;
      }

      try {
        const response = await axios.put(`http://localhost:8000/api/admin/users/${id}`, {
          name,
          email,
          role
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });

        const index = this.users.findIndex(user => user.id === id);
        if (index !== -1) {
          this.users.splice(index, 1, response.data);
        }
        this.clearEditingUser();
      } catch (error) {
        this.error = error.response?.data?.message || 'An error occurred while updating the user.';
      }
    },
    clearEditingUser() {
      this.editingUser = { id: null, name: '', email: '', role: '' }; 
    },
    logout() {
      localStorage.removeItem('token');
      this.$router.push('/'); 
    }
  },
};
</script>

<style scoped>
.error {
  color: red;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}
th,
td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: left;
}
th {
  background-color: #f4f4f4;
}
button {
  margin: 5px;
}
.add-task-form {
  background: #f9f9f9; 
  border: 1px solid #e0e0e0; 
  border-radius: 8px; 
  padding: 15px; 
  margin-top: 20px; 
}

.add-task-form input,
.add-task-form textarea {
  width: 90%; 
  margin: 10px 0; 
  padding: 10px; 
  border: 1px solid #ccc; 
  border-radius: 4px; 
}

.add-task-form select {
  width: 95%; 
  margin: 10px 0; 
  padding: 10px; 
  border: 1px solid #ccc; 
  border-radius: 4px; 
}


.header {
  display: flex; 
  justify-content: space-between; 
  align-items: center; 
  margin-bottom: 20px; 
}

.logout {
  background-color: #dc3545; 
  color: white;
  border: none;
  border-radius: 4px;
  padding: 10px 15px;
  cursor: pointer;
}

.logout:hover {
  background-color: #c82333; 
}

button {
  background-color: #28a745; 
  color: white;
  border: none;
  border-radius: 4px;
  padding: 10px;
  cursor: pointer;
  margin-right: 5px; 
}

button:hover {
  background-color: #218838; 
}
.add-task-card{
  background: #ffffff; 
  border: 1px solid #e0e0e0; 
  border-radius: 8px;
  padding: 15px; 
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
  width: calc(33% - 20px); 
  box-sizing: border-box; 
  margin-top: 15px;
}
</style>
