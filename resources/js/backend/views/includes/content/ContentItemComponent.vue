<template>

    <div>
        <b-card
            :title="contents.name"
            class="mb-2"
        >
            <b-card-text>
                Author: {{ contents.author_name}}
            </b-card-text>

            <b-card-text>
                Status: {{ contents.status_name}}
            </b-card-text>

        </b-card>
    </div>

</template>

<script>

export default {
    props: ['contents'],
}

</script>

