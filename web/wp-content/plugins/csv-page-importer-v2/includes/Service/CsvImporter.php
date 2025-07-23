<?php
namespace CPIv2\Service;

if ( ! defined( 'ABSPATH' ) ) exit;

class CsvImporter {

    /** @return array<int,array<string,string>> */
    public function parse( string $file ): array {
        if ( ! file_exists( $file ) ) {
            throw new \Exception( 'CSV file not found.' );
        }
        $fh = fopen( $file, 'r' );
        if ( ! $fh ) {
            throw new \Exception( 'Cannot open CSV file.' );
        }

        $headers = [];
        $rows    = [];
        $line    = 0;

        while ( ( $data = fgetcsv( $fh ) ) !== false ) {
            $line++;
            if ( $line === 1 ) {
                $headers = array_map( [ $this, 'normalize_key' ], $data );
                continue;
            }
            if ( $this->is_empty_row( $data ) ) continue;
            $rows[] = array_map( 'trim', array_combine( $headers, $data ) );
        }
        fclose( $fh );

        if ( empty( $rows ) ) {
            throw new \Exception( 'CSV has no data rows.' );
        }

        return $rows;
    }

    private function normalize_key( string $key ): string {
        return strtolower( preg_replace( '/[^a-z0-9_]+/i', '_', trim( $key ) ) );
    }

    private function is_empty_row( array $data ): bool {
        foreach ( $data as $v ) {
            if ( trim( (string) $v ) !== '' ) return false;
        }
        return true;
    }
}
