<template>
  <div class="container">
    <h1>Project Manager</h1>
    

    <button @click="openCreateModal" class="btn btn-primary btn-large">
      + New Project
    </button>


    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else>
      <div v-if="projects.length === 0" class="empty-state">
        No projects yet. Click "New Project" to create one!
      </div>
      <div 
        v-for="project in projects" 
        :key="project.id"
        class="project-card"
      >
        <div class="project-header">
          <h3>{{ project.attributes.name }}</h3>
          <span class="status" :class="project.attributes.status">
            {{ project.attributes.status }}
          </span>
        </div>
        <p class="description">{{ project.attributes.description || 'No description' }}</p>
        
 
        <div class="tasks-section">
          <h4>Tasks ({{ project.relationships?.tasks?.data?.length || 0 }})</h4>
          <div v-if="project.relationships?.tasks?.data?.length" class="task-list">
            <div 
              v-for="task in project.relationships.tasks.data" 
              :key="task.id"
              class="task-item"
              :class="{ 'task-completed': task.attributes.completed }"
            >
              <div class="task-content">
                <input 
                  type="checkbox" 
                  :checked="task.attributes.completed"
                  @change="toggleTask(task, project.id)"
                />
                <span>{{ task.attributes.title }}</span>
              </div>
              <button @click="deleteTask(task.id, project.id)" class="btn-icon">×</button>
            </div>
          </div>
          <div v-else class="no-tasks">No tasks yet</div>
          

          <div class="add-task">
            <input 
              v-model="newTaskTitle[project.id]" 
              @keyup.enter="addTask(project.id)"
              placeholder="Add a task..."
              class="task-input"
            />
            <button @click="addTask(project.id)" class="btn btn-small">Add</button>
          </div>
        </div>

        <div class="project-footer">
          <div>
            <button @click="openEditModal(project)" class="btn-icon">✏️</button>
            <button @click="deleteProject(project.id)" class="btn-icon">🗑️</button>
          </div>
        </div>
      </div>
    </div>

    <EditProjectModal
      :show="showModal"
      :project="editingProject"
      @close="closeModal"
      @save="handleSave"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { projectService, taskService } from '../services/api';
import EditProjectModal from './EditProjectModal.vue';

const projects = ref([]);
const loading = ref(false);
const error = ref<string | null>(null);
const showModal = ref(false);
const editingProject = ref(null);
const newTaskTitle = ref<Record<number, string>>({});

async function fetchProjects() {
  loading.value = true;
  error.value = null;
  try {
    const response = await projectService.getAll();
    projects.value = response.data || [];
  } catch (err: any) {
    error.value = 'Failed to load projects. Make sure the backend is running.';
    console.error(err);
  } finally {
    loading.value = false;
  }
}

function openCreateModal() {
  editingProject.value = null;
  showModal.value = true;
}

function openEditModal(project: any) {
  editingProject.value = project;
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingProject.value = null;
}

async function handleSave(formData: any) {
  try {
    if (editingProject.value) {
      await projectService.update(editingProject.value.id, formData);
    } else {
      await projectService.create(formData);
    }
    closeModal();
    await fetchProjects();
  } catch (err: any) {
    error.value = editingProject.value ? 'Failed to update project' : 'Failed to create project';
    console.error(err);
  }
}

async function deleteProject(id: number) {
  if (!confirm('Are you sure you want to delete this project?')) return;
  
  try {
    await projectService.delete(id);
    await fetchProjects();
  } catch (err: any) {
    error.value = 'Failed to delete project';
    console.error(err);
  }
}

async function addTask(projectId: number) {
  const title = newTaskTitle.value[projectId]?.trim();
  if (!title) return;
  
  try {
    await taskService.create({
      project_id: projectId,
      title: title,
      description: '',
      priority: 'medium'
    });
    newTaskTitle.value[projectId] = '';
    await fetchProjects(); 
  } catch (err: any) {
    error.value = 'Failed to add task';
    console.error(err);
  }
}

async function toggleTask(task: any, projectId: number) {
  try {
    await taskService.update(task.id, {
      completed: !task.attributes.completed
    });
    await fetchProjects(); 
  } catch (err: any) {
    error.value = 'Failed to update task';
    console.error(err);
  }
}

async function deleteTask(taskId: number, projectId: number) {
  if (!confirm('Delete this task?')) return;
  
  try {
    await taskService.delete(taskId);
    await fetchProjects(); 
  } catch (err: any) {
    error.value = 'Failed to delete task';
    console.error(err);
  }
}

onMounted(() => {
  fetchProjects();
});
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

h1 {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
}

.btn-large {
  width: 100%;
  padding: 12px;
  font-size: 16px;
  margin-bottom: 20px;
}

.project-card {
  background: white;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.project-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.project-header h3 {
  margin: 0;
  color: #333;
}

.status {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
  text-transform: capitalize;
}

.status.planning { background-color: #ffd700; color: #333; }
.status.active { background-color: #4CAF50; color: white; }
.status.completed { background-color: #9e9e9e; color: white; }
.status.on-hold { background-color: #f44336; color: white; }

.description {
  color: #666;
  margin: 10px 0;
  line-height: 1.5;
}

.tasks-section {
  margin: 15px 0;
  padding: 15px 0;
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
}

.tasks-section h4 {
  margin: 0 0 10px 0;
  color: #555;
}

.task-list {
  margin-bottom: 10px;
}

.task-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px;
  background-color: #f9f9f9;
  border-radius: 4px;
  margin-bottom: 5px;
}

.task-content {
  display: flex;
  align-items: center;
  gap: 8px;
}

.task-content input[type="checkbox"] {
  cursor: pointer;
}

.task-completed span {
  text-decoration: line-through;
  color: #999;
}

.no-tasks {
  color: #999;
  font-style: italic;
  padding: 10px 0;
}

.add-task {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.task-input {
  flex: 1;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.project-footer {
  display: flex;
  justify-content: flex-end;
  gap: 5px;
}

.btn, .btn-icon {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-primary {
  background-color: #4CAF50;
  color: white;
}

.btn-primary:hover {
  background-color: #45a049;
}

.btn-small {
  padding: 4px 8px;
  font-size: 12px;
  background-color: #4CAF50;
  color: white;
}

.btn-icon {
  padding: 4px 8px;
  background: none;
  font-size: 16px;
}

.btn-icon:hover {
  background-color: #f0f0f0;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
}

.error {
  text-align: center;
  padding: 20px;
  color: #f44336;
  background-color: #ffebee;
  border-radius: 4px;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: #999;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
