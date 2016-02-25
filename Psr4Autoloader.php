<?php

    /**
     *
     *  一个符合psr-4规范的文件加载器
     *
     *  $loader = new Psr4Autoloader();
     *  $loader->register();
     *
     *  $loader->addNamespace('Jaylee', __DIR__);
     *
     *  new Jaylee\Base\Object();   //__DIR__.'/'.Base/Object.php
     */

    class Psr4Autoloader {

        protected $prefix = [];

        public function register(){
            spl_autoload_register( [$this, 'loadClass'] );
        }

        public function addNamespace( $prefix, $path, $prepend = false){

            $prefix = trim($prefix, '\\');
            $path = rtrim($path, '\\') . DIRECTORY_SEPARATOR;

            if ( false === isset($this->prefix[$prefix]) ){
                $this->prefix[$prefix] = [];
            }

            if ( $prepend ){
                array_unshift($this->prefix[$prefix], $path);
            } else {
                array_push($this->prefix[$prefix], $path);
            }
        }

        public function loadClass( $class ){

            $prefix = $class;

            while( false !== $pos = strrpos( $prefix, '\\' ) ){

                $prefix = rtrim(substr($class, 0, $pos + 1), '\\');

                $relative_class = substr($class, $pos + 1);

                $mappedFile = $this->loadMappedFile( $prefix, $relative_class );

                if ( $mappedFile ) {
                    return $mappedFile;
                }
            }

            return false;
        }

        protected function loadMappedFile( $prefix, $relative_class ){

            if ( isset( $this->prefix[$prefix] ) === false  ){
                return false;
            }

            foreach( $this->prefix[$prefix] as $base_dir ){

                $file = $base_dir . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) .'.php';

                if ( $this->requireFile($file) ){
                    return $file;
                } else {
                    return false;
                }
            }
        }

        protected function requireFile( $file ){

            if ( file_exists( $file ) ){
                require $file;
                return true;
            } else {
                return false;
            }
        }
    }

    $autoload = new Psr4Autoloader();

    $autoload->register();

    $autoload->addNamespace('Jaylee', __DIR__);
    $autoload->addNamespace('Jaylee', __DIR__.'/Jaylee');

    new Jaylee\Foo\Bar\Aaa();

//    new Jaylee\Aaa();
