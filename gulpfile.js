const config = {
    filesToCompileScss: "www/sass/*.scss",
    outputFolderCss: "www/assets/css",
};

const { src, dest } = require("gulp");
const cleanCss = require("gulp-clean-css");
const sass = require("gulp-sass");

function generateCss() {
    return src(config.filesToCompileScss)
        .pipe(sass())
        .pipe(cleanCss())
        .pipe(dest(config.outputFolderCss));
}

function icons() {
    return src("node_modules/@fortawesome/fontawesome-free/webfonts/*")
        .pipe(dest("www/assets/webfonts/"));
}

function defaultTask(cb) {
    generateCss();
    icons();
    cb();
}

exports.default = defaultTask;
exports.css = generateCss();
exports.icons = icons();