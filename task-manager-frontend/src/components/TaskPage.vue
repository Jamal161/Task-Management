<template>
  <div>
    <div class="header">
      <h1>Tasks List</h1>
      <button class="logout" @click="logout">Logout</button>
    </div>

    <div class="task-list">
      <div v-for="task in tasks" :key="task.id" class="task-card">
        <h3>{{ task.title }}</h3>
        <p><strong>Status:</strong> {{ task.status }}</p>
        <p><strong>Description:</strong> {{ task.description }}</p>
        <p><strong>Due Date:</strong> {{ task.due_date }}</p>
        <button @click="editTask(task)">Edit</button>
        <button @click="deleteTask(task.id)">Delete</button>
      </div>
    </div>

    <div class="task-forms-container">
      <div class="add-task-card">
        <h2>Add New Task</h2>
        <form @submit.prevent="addTask" class="add-task-form">
          <input v-model="newTask.title" placeholder="Task Title" required />
          <textarea v-model="newTask.description" placeholder="Task Description"></textarea>
          <select v-model="newTask.status" required>
            <option value="Pending">Pending</option>
            <option value="In-Progress">In-Progress</option>
            <option value="Completed">Completed</option>
            <option value="Rejected">Rejected</option>
          </select>
          <input v-model="newTask.due_date" type="date" required />
          <button type="submit">Add Task</button>
        </form>
      </div>

      <div v-if="editingTask" class="add-task-card">
        <h2>Edit Task</h2>
        <form @submit.prevent="updateTask" class="add-task-form">
          <input v-model="editingTask.title" placeholder="Task Title" required />
          <textarea v-model="editingTask.description" placeholder="Task Description"></textarea>
          <select v-model="editingTask.status" required>
            <option value="Pending">Pending</option>
            <option value="In-Progress">In-Progress</option>
            <option value="Completed">Completed</option>
            <option value="Rejected">Rejected</option>
          </select>
          <input v-model="editingTask.due_date" type="date" required />
          <button type="submit">Update Task</button>
          <button type="button" @click="cancelEdit">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</template>



<script>
import axios from 'axios';

const token = localStorage.getItem('token');
console.log(token); 

export default {
  data() {
    return {
      tasks: [],
      newTask: {
        title: '',
        description: '',
        status: 'Pending',
        due_date: ''
      },
      editingTask: null,
    };
  },
  async mounted() {
    await this.fetchTasks();
  },
  methods: {
    async fetchTasks() {
      const token = localStorage.getItem('token');
      try {
        const response = await axios.get('http://localhost:8000/api/tasks', {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.tasks = response.data;
      } catch (error) {
        console.error('Error fetching tasks:', error);
      }
    },
    async addTask() {
      const token = localStorage.getItem('token');
      try {
        const response = await axios.post('http://localhost:8000/api/tasks', this.newTask, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.tasks.push(response.data); 
        this.resetNewTask(); 
      } catch (error) {
        console.error('Error adding task:', error);
      }
    },
    resetNewTask() {
      this.newTask = {
        title: '',
        description: '',
        status: 'Pending',
        due_date: ''
      };
    },
    editTask(task) {
      this.editingTask = { ...task }; 
    },
    async updateTask() {
  const token = localStorage.getItem('token');
  try {
    const response = await axios.put(`http://localhost:8000/api/tasks/${this.editingTask.id}`, this.editingTask, {
      headers: { Authorization: `Bearer ${token}` },
    });
    const index = this.tasks.findIndex(task => task.id === this.editingTask.id);
    this.tasks.splice(index, 1, response.data); 
    this.cancelEdit(); 
  } catch (error) {
    console.error('Error updating task:', error);
  }
},
    async deleteTask(taskId) {
      const token = localStorage.getItem('token');
      try {
        await axios.delete(`http://localhost:8000/api/tasks/${taskId}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.tasks = this.tasks.filter(task => task.id !== taskId); 
      } catch (error) {
        console.error('Error deleting task:', error);
      }
    },
    cancelEdit() {
      this.editingTask = null; 
    },

    logout() {
      localStorage.removeItem('token'); 
      this.$router.push('/'); 
    }
  },
};
</script>

<style scoped>

.task-forms-container {
  display: flex;
  gap: 20px; 
  margin-top: 20px; 
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

.task-list {
  display: flex;
  flex-wrap: wrap; 
  gap: 20px; 
}

.task-card {
  background: #ffffff; 
  border: 1px solid #e0e0e0; 
  border-radius: 8px; 
  padding: 15px; 
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
  width: calc(33% - 20px); 
  box-sizing: border-box; 
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
.task-card h3 {
  margin: 0 0 10px 0; 
  font-size: 1.2em; 
}

.task-card p {
  margin: 5px 0; 
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
</style>

