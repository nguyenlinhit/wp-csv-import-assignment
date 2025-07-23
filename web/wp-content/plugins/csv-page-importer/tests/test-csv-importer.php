<?php

class Test_CSV_Importer extends WP_UnitTestCase{
    /**
     * @throws Exception
     */
    public function test_parse_valid_csv() {
        $csv = "title,content,slug\nHello,World,hello\n";
        $tmp = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tmp, $csv);

        $importer = new \CPI\CSV_Importer();
        $rows = $importer->parse($tmp);
        var_dump( class_exists('\CPI\CSV_Importer') );

        $this->assertCount(1, $rows);
        $this->assertSame('Hello', $rows[0]['title']);
        $this->assertSame('World', $rows[0]['content']);
        unlink($tmp);
    }

    public function test_parse_missing_file_throw() {
        $this->expectException(\Exception::class);
        (new \CPI\CSV_Importer())->parse('/nope.csv');
    }
}