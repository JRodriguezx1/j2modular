const { src, dest, watch, parallel } = require('gulp');

const esbuild = require('esbuild');
// CSS
const sass = require('gulp-sass')(require('sass'));
const plumber = require('gulp-plumber');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const postcss = require('gulp-postcss');
const sourcemaps = require('gulp-sourcemaps');
// Imagenes
const cache = require('gulp-cache');
const imagemin = require('gulp-imagemin');
//const webp = require('gulp-webp');
const notify = require('gulp-notify');
const avif = require('gulp-avif');
// Javascript
const terser = require('gulp-terser-js');
const concat = require('gulp-concat');
const rename = require('gulp-rename')
// Typescript
const ts = require('gulp-typescript');

const paths = {
    scss: 'src/scss/**/*.scss',
    js: 'src/js/**/*.js',
    ts: 'src/ts/**/*.ts',
    imagenes: 'src/img/**/*'
}
// Crear proyecto TypeScript
const tsProject = ts.createProject('tsconfig.json');


async function restaurantTypescript() {
    await esbuild.build({
        entryPoints: ['app/Modules/Restaurant/Resources/ts/main.ts'],
        bundle: true,
        outdir: 'public/build/js/restaurant',
        format: 'esm',
        splitting: true,
        sourcemap: true,
        minify: true,
        target: ['es2020']
    });
}


function css() {
    return src(paths.scss)
        .pipe( sourcemaps.init())
        .pipe( sass({outputStyle: 'expanded'}))
        .pipe( postcss([autoprefixer(), cssnano()])) /////autoprefixer me agrega prefijos como webkit- de manera automatica
        .pipe( sourcemaps.write('.'))
        .pipe(  dest('public/build/css') );
}
function javascript() {
    return src(paths.js)
      .pipe(sourcemaps.init())
      .pipe(concat('bundle.js')) 
      .pipe(terser())
      .pipe(sourcemaps.write('.'))
      .pipe(rename({ suffix: '.min' }))
      .pipe(dest('./public/build/js'))
}

function typescript() {
    return src(paths.ts)
      .pipe(sourcemaps.init())
      .pipe(tsProject()) // Compila TypeScript
      .pipe(concat('bundle.ts.js')) // Concatena el TS compilado
      .pipe(terser()) // Minifica el archivo TS compilado
      .pipe(sourcemaps.write('.'))
      .pipe(rename({ suffix: '.min' })) // Renombra el archivo resultante
      .pipe(dest('./public/build/js')); // Envía el archivo a la carpeta de destino
}

function imagenes() {
    return src(paths.imagenes)
        .pipe( cache(imagemin({ optimizationLevel: 3})))
        .pipe( dest('public/build/img'))
        .pipe(notify({message: 'imagen completada'}))
}

//function versionWebp( done ) {
//    const opciones = {
//        quality: 50
//    };
//    src('src/img/**/*.{png,jpg}')
//        .pipe( webp(opciones) )
//        .pipe( dest('public/build/img') )
//    done();
//}

function versionAvif( done ) {
    const opciones = {
        quality: 50
    };
    src('src/img/**/*.{png,jpg}')
        .pipe( avif(opciones) )
        .pipe( dest('public/build/img') )
    done();
}

function app(done) {
    watch( paths.scss, css );
    watch( paths.js, javascript );
    watch( paths.ts, typescript );  //POS
    watch('app/Modules/Restaurant/Resources/ts/**/*.ts', restaurantTypescript); //restaurant
    watch( paths.imagenes, imagenes)
    //watch( paths.imagenes, versionWebp)
    watch( paths.imagenes, versionAvif)
    done()
}

exports.css = css;
exports.js = javascript;
exports.ts = typescript;
exports.restaurantTs = restaurantTypescript;
exports.imagenes = imagenes;
//exports.versionWebp = versionWebp;
exports.versionAvif = versionAvif;
exports.app = parallel( css, imagenes, /*versionWebp,*/ versionAvif, javascript, typescript, restaurantTypescript, app) ;