module.exports = {
  purge: [
        '../templates/*.twig',
  ],
  theme: {
    colors: {
      background: '#191919',
      container: '#191919',
      font: '#111827',
      link: '#111827',
      backgroundmenu: '#111827',
      textcolormenu: '#79889c',
      titlecolor: '#d7dadc',
      boxbackgroundcolor: '#111827',
      boxbordercolor: '#4b5563',
      boxheading: '#d7dadc',
      boxcontent: '#d7dadc',
      groupelement: '#1a253c'
    },
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
  },
  variants: {
    extend:  {
      scale: ['focus-within']
    }
  },
  plugins: []
}