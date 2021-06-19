const yaml = require('yaml')
const fs = require('fs');
const path = require('path');
const configFilePath = path.join(__dirname, '../config/config.yml')
const configParsed = yaml.parse(fs.readFileSync(configFilePath).toString());
const safeListParsed = yaml.parse(fs.readFileSync(path.join(__dirname,'../cache/whitelist.yml')).toString());
module.exports = {
  mode: 'jit',
  purge: {
        content: [
          '../templates/**/*.twig',
          '../config/config.yml',
          '../config/overwrittes/*.twig'
        ],
        safelist: safeListParsed
  },
  theme: {
    colors: configParsed.theme.colors,
    fontFamily: {
      sans: ['Verdana', 'sans-serif'],
      serif: ['Georgia', 'serif'],
    },
    fontSize: {
        xs: '0.75rem',
        sm: '0.875rem',
        base: '1rem',
        lg: '1.125rem',
        xl: '1.25rem',
        '2xl': '1.5rem',
        '3xl': '1.875rem',
        '4xl': '2.25rem',
        '5xl': '3rem',
        '6xl': '4rem',
    },
    fontWeight: {
        hairline: '100',
        thin: '200',
        light: '300',
        normal: '400',
        medium: '500',
        semibold: '600',
        bold: '700',
        extrabold: '800',
        black: '900',
    },
  }
}