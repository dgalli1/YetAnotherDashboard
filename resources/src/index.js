import './css/style.js';
import './js/**/*.js';

// Make all the images from the icons folder known to webpack
function importAll(r) {
    return r.keys().map(r);
  }
const images = importAll(require.context('../../icons/', false, /\.(png|jpe?g|svg|gif|ico|webp|avif)$/));