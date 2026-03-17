<template>
  <div class="container">
    <button @click="goBack" class="btn">← Back</button>
    <h1>Project Detail</h1>
    <p>Project ID: {{ $route.params.id }}</p>
    <p>Coming soon: Tasks will be displayed here</p>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';

const router = useRouter();

function goBack() {
  router.push('/');
}
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.btn {
  padding: 8px 16px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-bottom: 20px;
}
</style>