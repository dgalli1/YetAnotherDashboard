const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require("terser-webpack-plugin");
const path = require("path");
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const { extendDefaultPlugins } = require("svgo");
const ImageMinimizerPlugin = require("image-minimizer-webpack-plugin");
module.exports = (env, options) => ({
  
  entry: {
      main: "./src/index.js",
      browser: "./src/browser.js"

  },
  devServer: {
    contentBase: "./res"
  },
  devtool: "source-map",
  module: {
    rules: [
      {
        test: /\.yml$/,
        use: [
          {
            loader: path.resolve('yaml-loader-custom.js'),
            options: {
              /* ... */
            },
          },
        ],
      },
      {
        test: /\.css$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader
          },
          {
            loader: 'css-loader',
          },
          {
            loader: 'postcss-loader',
            options: {
              postcssOptions: {
                plugins:
                  [
                    require('postcss-import'),
                    require('tailwindcss'),
                    require('postcss-preset-env')({
                      stage: 1,
                      features: {
                        'focus-within-pseudo-class': false
                      }
                    }),
                    require('autoprefixer')
                  ]
              }
            }
          },
        ]
      },
      {
        test: /\.(png|jpg|gif|webp|avif)$/,
        use: [
          {
            loader: "file-loader",
            options: {
              name: "[name].[ext]",
              outputPath: "images/",
              publicPath: "/images"
            }
          },
          {
            loader: "webpack-image-resize-loader",
            options: {
              width: 64,
            },
          },
        ]
      },
      {
        test: /\.(svg)$/,
        use: [
          {
            loader: "file-loader",
            options: {
              name: "[name].[ext]",
              outputPath: "images/",
              publicPath: "/images"

            }
          }
        ]
      },
      {
        test: /\.(nothingatall)$/i,
        use: [
          {
            loader: 'raw-loader',
            options: {
              esModule: false,
            },
          },
        ]
      },
      {
        test: /\.(ttf|otf|eot|woff|woff2)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
        use: [
          {
            loader: "file-loader",
            options: {
              name: "[name].[ext]",
              outputPath: "fonts/",
              publicPath: "/fonts"
            }
          }
        ]
      },
      {
        test: /\.js$/,
        use: [
          'webpack-import-glob-loader',
            {
              loader: "babel-loader",
              options: {
                "exclude": [
                  // \\ for Windows, \/ for Mac OS and Linux
                  /node_modules[\\\/]core-js/,
                  /node_modules[\\\/]/,
                  /gulp[\\\/]sass/
                ],
                presets: [
                  [
                    "@babel/preset-env",
                    {
                      useBuiltIns: "usage",
                      corejs: 3,
                    }
                  ]
                ]
            }
          }
        ]

      }
    ]
  },
  plugins: [
    // new BundleAnalyzerPlugin(), //uncomment to analyze which js libary takes how much space
    new MiniCssExtractPlugin({
      filename: 'css/[name].css'
    }),
    new ImageMinimizerPlugin({
      minimizerOptions: {
        // Lossless optimization with custom option
        // Feel free to experiment with options for better result for you
        plugins: [
          ["gifsicle", { interlaced: true }],
          ["jpegtran", { progressive: true }],
          ["optipng", { optimizationLevel: 5 }],
          // Svgo configuration here https://github.com/svg/svgo#configuration
          [
            "svgo",
            {
              plugins: extendDefaultPlugins([
                {
                  name: "removeViewBox",
                  active: false,
                },
                {
                  name: "addAttributesToSVGElement",
                  params: {
                    attributes: [{ xmlns: "http://www.w3.org/2000/svg" }],
                  },
                },
              ]),
            },
          ],
        ],
      },
    }),
  ],
  optimization: {
    minimize: true,
    minimizer: [
        new TerserPlugin({
          extractComments: true,
          parallel: true,
        }),
        new CssMinimizerPlugin({
          minimizerOptions: {
            preset: [
              'default',
              {
                discardComments: { removeAll: true },
              },
            ]
          }
        })
    ],
  },
  output: {
    filename: "js/[name].js",
    path: path.resolve(__dirname,'..','webroot', "res"),
  }
});
