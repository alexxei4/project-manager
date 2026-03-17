<template>
  <div v-if="show" class="modal-overlay" @click="close">
    <div class="modal-content" @click.stop>
      <h2>{{ project ? 'Edit Project' : 'Add Project' }}</h2>
      
      <form @submit.prevent="save">
        <div class="form-group">
          <label>Project Name</label>
          <input v-model="form.name" type="text" required class="input" />
        </div>
        
        <div class="form-group">
          <label>Description</label>
          <textarea v-model="form.description" class="textarea" rows="3"></textarea>
        </div>
        
        <div class="form-group">
          <label>Status</label>
          <select v-model="form.status" class="select">
            <option value="planning">Planning</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
            <option value="on-hold">On Hold</option>
          </select>
        </div>
        
        <div class="form-group">
          <label>Deadline</label>
          <input v-model="form.deadline" type="date" class="input" />
        </div>
        
        <div class="modal-actions">
          <button type="button" @click="close" class="btn btn-secondary">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
  show: boolean;
  project?: any | null;
}>();

const emit = defineEmits(['close', 'save']);

const form = ref({
  name: '',
  description: '',
  status: 'planning',
  deadline: ''
});

watch(() => props.project, (newProject) => {
  if (newProject) {
    form.value = {
      name: newProject.attributes.name,
      description: newProject.attributes.description || '',
      status: newProject.attributes.status,
      deadline: newProject.attributes.deadline || ''
    };
  } else {
    form.value = {
      name: '',
      description: '',
      status: 'planning',
      deadline: ''
    };
  }
}, { immediate: true });

function save() {
  emit('save', form.value);
}

function close() {
  emit('close');
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 8px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

h2 {
  margin-top: 0;
  color: #333;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 5px;
  color: #666;
  font-weight: 500;
}

.input, .textarea, .select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.textarea {
  resize: vertical;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.btn {
  padding: 10px 20px;
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

.btn-secondary {
  background-color: #f0f0f0;
  color: #333;
}

.btn-secondary:hover {
  background-color: #e0e0e0;
}
</style>