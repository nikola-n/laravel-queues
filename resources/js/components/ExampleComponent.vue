<template>
    <div class="container">

        <ul>
            <li v-for="task in tasks" v-text="task"></li>
        </ul>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                tasks:[]
            }
        },
        created() {
            axios.get('/tasks')
            .then(response => (this.tasks = response.data));
        }
    }
</script>
