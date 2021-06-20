import './css/style.js';
// Make all the images from the icons folder known to webpack
function importAll(r) {
    return r.keys().map(r);
  }
const images = importAll(require.context('../../config/icons/', false, /\.(png|jpe?g|svg|gif|ico|webp|avif)$/));