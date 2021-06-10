import Vue from 'vue';

import VueProgressBar from 'vue-progressbar'

const options = {
  color: '#20a8d8',
  failedColor: '#874b4b',
  thickness: '5px',
  transition: {
    speed: '0.2s',
    opacity: '0.6s',
    termination: 300
  },
};

Vue.use(VueProgressBar, options);