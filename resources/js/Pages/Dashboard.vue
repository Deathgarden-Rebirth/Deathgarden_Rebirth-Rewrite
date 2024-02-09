<script setup>

import { Head } from '@inertiajs/vue3'

defineProps({
  test: String,
})
</script>

<template>
    <Head title="Welcome" />
    <h1>Welcome</h1>
    <p>Hello {{ test }} welcome to your first Inertia app!</p>
</template>

<style scoped>

</style>