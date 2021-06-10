<template>
    <v-col cols="6">
        <v-text-field
            required
            :label="root_parent.form.template.variables[index].variable_name"
            v-if="root_parent.form.template.variables[index].variable_type=='Text'" 
            v-model="root_parent.form.template.variables[index].value">
        </v-text-field>
        <v-textarea
            required
            :label="root_parent.form.template.variables[index].variable_name"
            v-if="root_parent.form.template.variables[index].variable_type=='Textarea'" 
            v-model="root_parent.form.template.variables[index].value">
        </v-textarea>
        <v-file-input 
            required
            v-model="file"
            v-if="root_parent.form.template.variables[index].variable_type=='Image'" 
            @change="onGetFile" accept=".png, .jpg, .jpeg" 
            :label="root_parent.form.template.variables[index].variable_name"></v-file-input>
    </v-col>
</template>

<script>

    export default {
        name: "variable_input",
        props: ["root_parent","parent","index"],
        data() {
            return {
                date_requested: "",
                variables: "",
                file: "",
                variable_type_options: [
                    "Text",
                    "Textarea",
                    "Image",
                ],
            };
        },
        methods: {
            onGetFile(event) {
                console.log("Changed file");
                this.root_parent.form.template.variables[this.index].value = this.file;
            },
            deleteVariable: function () {
                let vm = this;
                
                $.confirm({
                    title: this.$t('confirm_action'),
                    content: vm.$t("label.delete_variable_confirmation"),
                    type: 'red',
                    typeAnimated: true,
                    buttons: {
                        tryAgain: {
                            text: this.$t('confirm'),
                            btnClass: 'btn-red',
                            action: function(){   
                                vm.root_parent.deleteVariable(vm.index);
                            }
                        },
                        cancel: function () {
                        }
                    }
                });
            },
        }
    };
</script>