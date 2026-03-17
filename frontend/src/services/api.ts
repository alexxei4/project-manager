import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

export const projectService = {
    async getAll() {
        const response = await api.get('/projects');
        return response.data;
    },

    async create(data: any) {
        const response = await api.post('/projects', data);
        return response.data;
    },

    async update(id: number, data: any) {
        const response = await api.put(`/projects/${id}`, data);
        return response.data;
    },

    async delete(id: number) {
        await api.delete(`/projects/${id}`);
    }
};

export const taskService = {
    async getAll(projectId?: number) {
        const url = projectId ? `/tasks?project_id=${projectId}` : '/tasks';
        const response = await api.get(url);
        return response.data;
    },

    async create(data: any) {
        const response = await api.post('/tasks', data);
        return response.data;
    },

    async update(id: number, data: any) {
        const response = await api.put(`/tasks/${id}`, data);
        return response.data;
    },

    async delete(id: number) {
        await api.delete(`/tasks/${id}`);
    }
};