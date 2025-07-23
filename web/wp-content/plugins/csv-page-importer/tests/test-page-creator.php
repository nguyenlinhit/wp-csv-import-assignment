<?php

class Test_Page_Creator extends WP_UnitTestCase {

    public function test_create_page_success() {
        $creator = new \CPI\Page_Creator();
        $row = [
            'title'   => 'My Page',
            'content' => 'Body',
            'slug'    => 'my-page'
        ];
        $pid = $creator->create_page($row, 123);

        $this->assertIsInt($pid);
        $post = get_post($pid);
        $this->assertSame('My Page', $post->post_title);
        $this->assertSame('draft', $post->post_status);
        $this->assertSame('my-page', $post->post_name);

        $this->assertEquals(123, get_post_meta($pid, '_cpi_batch_id', true));
        $meta_row = get_post_meta($pid, '_cpi_row', true);
        $this->assertSame($row, $meta_row);
    }
}