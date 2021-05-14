<template>
    <div class="row">
        <div class="col-md-8">
            <h3 v-text="project.name"></h3>
            <ul>
                <li v-for="task in project.tasks" v-text="task.body"></li>
            </ul>

            <input class="form-control" type="text" v-model="newTask" @blur="save" placeholder="New Task" @keydown="tapPeers">
            <span v-if="activePeer" v-text="activePeer.name + ' is typing... '"></span>
        </div>

        <div class="col-md-4">
            <h4>Active Participants</h4>
        </div>
        <ul>
            <li v-for="participant in participants" v-text="participant.name">

            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: ['data-project'], //accept the project prop
    data() {
        return {
            project: this.dataProject,
            // tasks: [],
            newTask: '',
            activePeer: false,
            typingTimer: false,
            participants: [],
        }
    },

    computed: {
        channel() {
            // join instead of private/channel allows 3 more methods on
            //echo, used for presence channel
            return window.Echo.join('tasks.' + this.project.id)
        }
    },
    created() {
        // axios.get(`/api/projects/${this.project.id}`)
        //     .then(response => (this.tasks = response.data))
        //     .listenForWhisper('typing', e => {
        //         this.activePeer = e;
        //     });


        // window.Echo.channel('tasks').listen('TaskCreated', e => {
        //     this.tasks.push(e.task.body)
        // });
        //instead of e  you can use use destructuring ({task})
        this.channel
            .here(users => {
                // triggered when the page loads
                // contains information about all users
                this.participants = users;
            })
            .joining(user => {
                // when user has joined a group this methods is fired
                this.participants.push(user);
            })
            .leaving(user => {
                // when user has left a group this methods is fired
                this.participants.splice(this.participants.indexOf(user), 1);
            })
            .listen('TaskCreated', ({task}) => this.addTask(task))
            .listenForWhisper('typing', this.flashActivePeer);
    },

    methods: {
        flashActivePeer(e) {

            this.activePeer = e;

            if (this.typingTimer) clearTimeout(this.typingTimer);

            this.typingTimer = setTimeout(() =>
                (this.activePeer = false), 3000);
        },
        tapPeers() {
            this.channel
                .whisper('typing', {
                    name: window.App.user.name,
                });
        },
        save() {
            axios.post(`/api/projects/${this.project.id}/tasks`, {
                body: this.newTask
            }).then(response => response.data)
                .then(this.addTask);
        },

        addTask(task) {
            this.activePeer = false;

            this.project.tasks.push(task);

            this.newTask = '';
        }
    }
}
</script>
