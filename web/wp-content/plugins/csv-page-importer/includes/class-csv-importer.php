<?php
namespace CPI;

if ( ! defined( 'ABSPATH' ) ) exit;

class CSV_Importer {

    /**
     * Parse uploaded CSV file and return array of rows (assoc).
     *
     * @param string $filepath Absolute path to CSV.
     * @return array [ [col=>val, ...], ... ]
     * @throws \Exception
     */
    public function parse( $filepath ) {
        if ( ! file_exists( $filepath ) ) {
            throw new \Exception( 'CSV file not found.' );
        }

        $rows = [];
        if ( ( $handle = fopen( $filepath, 'r' ) ) !== false ) {

            // Read header
            $headers = fgetcsv( $handle, 0, ',' );
            if ( ! $headers ) {
                throw new \Exception( 'CSV header missing or unreadable.' );
            }

            // Normalize headers to lowercase/trim
            $headers = array_map( function( $h ) {
                return strtolower( trim( $h ) );
            }, $headers );

            while ( ( $data = fgetcsv( $handle, 0, ',' ) ) !== false ) {
                if ( count( $data ) == 1 && $data[0] === null ) {
                    continue; // skip empty lines
                }
                $row = [];
                foreach ( $headers as $i => $col ) {
                    $row[ $col ] = isset( $data[ $i ] ) ? trim( $data[ $i ] ) : '';
                }
                $rows[] = $row;
            }
            fclose( $handle );
        } else {
            throw new \Exception( 'Cannot open CSV file.' );
        }

        return $rows;
    }
}
