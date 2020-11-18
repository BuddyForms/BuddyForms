module.exports = (ctx) => ({
  map: ctx.options.map,
  plugins: [
    require('postcss-prefixer')({
        prefix: 'tk-'
    })
  ]
});
