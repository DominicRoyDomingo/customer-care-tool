<template>
    <!-- <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
    <img src="{{ $logged_in_user->picture }}" class="img-avatar" alt="{{ $logged_in_user->email }}">
    <span class="d-md-down-none">{{ $logged_in_user->full_name }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-right" id="organizations_links">
    <div class="dropdown-header text-center">
        <strong>Account</strong>
    </div>
    <a class="dropdown-item" href="{{ route('frontend.auth.logout') }}">
        <i class="fas fa-lock"></i> @lang('navs.general.logout')
    </a>
    </div> -->
    <div>
        <v-menu
        :close-on-content-click="false"
        :nudge-width="200"
        offset-x
        >
            <template v-slot:activator="{ on, attrs }">
                <v-btn 
                    icon 
                    width="auto" 
                    height="auto"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-avatar
                        color="#54c0ff"
                        size="40"
                        tile
                        style="border-radius: 5px !important;"
                    >
                    </v-avatar>
                </v-btn> 
            </template>

            <v-card>
                <v-list>
                    <v-list-item>
                        <a class="dropdown-item" href="/logout">
                            LOGOUT
                        </a>
                    </v-list-item>
                </v-list>
            </v-card>
        </v-menu>
        <v-btn icon @click.stop="drawer = !drawer">
            <v-icon color="#fff">mdi-dots-vertical</v-icon>
        </v-btn>

        <v-navigation-drawer
            v-model="drawer"
            app
            right
            temporary
            width="500"
        >
            <v-list
            dense
            rounded
            >
            <v-list-item
            >
                <v-list-item-icon>
                <v-icon>test</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                <v-list-item-title>test</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            </v-list>
        </v-navigation-drawer>
    </div>
</template>

<script>

export default {

    props: ["user"],

    data: function() {

        return {

            drawer: false,
            group: null,

        };
    },

    watch: {
      group () {
        this.drawer = false
      },
    },
};
</script>

<style lang="scss">
</style>
