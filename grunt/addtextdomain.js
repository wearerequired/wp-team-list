module.exports = {
  dist: {
    options: {
      textdomain   : 'inline-notes',
      updateDomains: []
    },
    target : {
      files: {
        src: ['*.php', '**/*.php', '!node_modules/**', '!tests/**']
      }
    }
  }
};