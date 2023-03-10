/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
// import './styles/app.css';


// uncomment this line for import sass
import './styles/app.scss';


// Import Vue
import Vue from "vue";
import App from './components/App';



// start the Stimulus application
import './bootstrap';


new Vue({
  el: '#app',
  render: h => h(App)
});
