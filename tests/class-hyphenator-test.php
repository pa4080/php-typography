<?php

/**
 * Test Hyphenator class.
 *
 * @coversDefaultClass \PHP_Typography\Hyphenator
 * @usesDefaultClass \PHP_Typography\Hyphenator
 */
class Hyphenator_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var Hyphenator
     */
    protected $h;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->h = new \PHP_Typography\Hyphenator();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Return encoded HTML string (everything except <>"').
     *
     * @param string $html
     */
    protected function clean_html( $html ) {
    	$convmap = array(0x80, 0x10ffff, 0, 0xffffff);

    	return str_replace( array('&lt;', '&gt;'), array('<', '>'), mb_encode_numericentity( htmlentities( $html, ENT_NOQUOTES, 'UTF-8', false ), $convmap ) );
    }

    /**
     * Helper function to generate a valid token list from strings.
     *
     * @param string $value
     * @param string $type Optional. Default 'word'.
     *
     * @return array
     */
    protected function tokenize( $value, $type = 'word' ) {
    	return array(
    		array(
	    		'type'  => $type,
	    		'value' => $value
    		)
    	);
    }

    /**
     * Helper function to generate a valid token list from strings.
     *
     * @param string $value
     * @param string $type Optional. Default 'word'.
     *
     * @return array
     */
    protected function tokenize_sentence( $value ) {
    	$words = explode( ' ', $value );
    	$tokens = array();

    	foreach ( $words as $word ) {
    		$token[] = array(
    			'type'  => 'word',
    			'value' => $word,
    		);
    	}

    	return $tokens;
    }

    /**
     *
     * @param string $expected_value
     * @param array $actual_tokens
     * @param string $message
     */
    protected function assertTokensSame( $expected_value, $actual_tokens, $message = '' ) {
    	foreach ( $actual_tokens as $index => $token ) {
    		$actual_tokens[ $index ]['value'] = $this->clean_html( $actual_tokens[ $index ]['value'] );
    	}

		if ( false !== strpos( $expected_value, ' ' ) ) {
			$expected = $this->tokenize_sentence( $expected_value );
		} else {
			$expected = $this->tokenize( $expected_value );
		}

    	return $this->assertSame( $expected, $actual_tokens, $message );
    }

    /**
     * @covers ::set_language
     */
    public function test_set_language()
    {
    	$h = $this->h;
    	//$h->hyphenation_exceptions = array(); // necessary for full coverage

		$h->set_language( 'en-US' );
		$this->assertAttributeNotEmpty( 'pattern', $h, 'Empty pattern array' );
		$this->assertAttributeGreaterThan( 0, 'pattern_max_segment', $h, 'Max segment size 0' );
 		$this->assertAttributeNotEmpty( 'pattern_exceptions', $h, 'Empty pattern exceptions array' );

 		$h->set_language( 'foobar' );
 		$this->assertFalse( isset( $h->pattern ) );
 		$this->assertFalse( isset( $h->pattern_max_segment ) );
 		$this->assertFalse( isset( $h->pattern_exceptions ) );

 		$h->set_language( 'no' );
 		$this->assertAttributeCount( 3, 'pattern', $h, 'Invalid Norwegian pattern.');
 		$this->assertAttributeGreaterThan( 0, 'pattern_max_segment', $h, 'Max segment size 0' );
 		$this->assertAttributeNotEmpty( 'pattern_exceptions', $h, 'Empty pattern exceptions array' ); // Norwegian has exceptions

 		$h->set_language( 'de' );
 		$this->assertAttributeCount( 3, 'pattern', $h, 'Invalid German pattern.');
 		$this->assertAttributeGreaterThan( 0, 'pattern_max_segment', $h, 'Max segment size 0' );
 		$this->assertAttributeEmpty( 'pattern_exceptions', $h, 'Unexpected pattern exceptions found' ); // no exceptions in the German pattern file
    }

    /**
     * @covers ::set_language
     */
    public function test_set_same_hyphenation_language()
    {
    	$h = $this->h;

    	$h->set_language( 'en-US' );
    	$this->assertAttributeNotEmpty( 'pattern', $h, 'Empty pattern array' );
    	$this->assertAttributeGreaterThan( 0, 'pattern_max_segment', $h, 'Max segment size 0' );
    	$this->assertAttributeNotEmpty( 'pattern_exceptions', $h, 'Empty pattern exceptions array' );

    	$h->set_language( 'en-US' );
    	$this->assertAttributeNotEmpty( 'pattern', $h, 'Empty pattern array' );
    	$this->assertAttributeGreaterThan( 0, 'pattern_max_segment', $h, 'Max segment size 0' );
    	$this->assertAttributeNotEmpty( 'pattern_exceptions', $h, 'Empty pattern exceptions array' );
    }

    /**
     * @covers ::set_min_length
     */
    public function test_set_min_length()
    {
		$this->h->set_min_length( 1 );
		$this->assertAttributeSame( 1, 'min_length', $this->h );

		$this->h->set_min_length( 2 );
		$this->assertAttributeSame( 2, 'min_length', $this->h );

		$this->h->set_min_length( 66 );
		$this->assertAttributeSame( 66, 'min_length', $this->h );
    }

    /**
     * @covers ::set_min_before
     */
    public function test_set_min_before()
    {
		$this->h->set_min_before( 0 );
		$this->assertAttributeSame( 0, 'min_before', $this->h );

		$this->h->set_min_before( 1 );
		$this->assertAttributeSame( 1, 'min_before', $this->h );

		$this->h->set_min_before( 66 );
		$this->assertAttributeSame( 66, 'min_before', $this->h );
    }

    /**
     * @covers ::set_min_after
     */
    public function test_set_min_after()
    {
		$this->h->set_min_after( 0 );
		$this->assertAttributeSame( 0, 'min_after', $this->h );

		$this->h->set_min_after( 1 );
		$this->assertAttributeSame( 1, 'min_after', $this->h );

		$this->h->set_min_after( 66 );
		$this->assertAttributeSame( 66, 'min_after', $this->h );
    }


    /**
     * @covers \PHP_Typography\Hyphenator::set_custom_exceptions
     */
    public function test_set_custom_exceptions_array()
    {
// 		$h = $this->h;
// 		$h->settings['hyphenationExceptions'] = array(); // necessary for full coverage
// 		$exceptions = array( "Hu-go", "Fö-ba-ß" );

// 		$h->set_custom_exceptions( $exceptions );
// 		$this->assertContainsOnly( 'string', $h->settings['hyphenationCustomExceptions'] );
// 		$this->assertArraySubset( array( 'hugo' => 'hu-go' ), $h->settings['hyphenationCustomExceptions'] );
// 		$this->assertArraySubset( array( 'föbaß' => 'fö-ba-ß' ), $h->settings['hyphenationCustomExceptions'] );
// 		$this->assertCount( 2, $h->settings['hyphenationCustomExceptions'] );
    }

    /**
     * @covers \PHP_Typography\Hyphenator::set_custom_exceptions
     */
    public function test_set_custom_exceptions_unknown_encoding()
    {
//     	$h = $this->h;
//     	$h->settings['hyphenationExceptions'] = array(); // necessary for full coverage
//     	$exceptions = array( "Hu-go", mb_convert_encoding( "Fö-ba-ß" , 'ISO-8859-2' ) );

//     	$h->set_custom_exceptions( $exceptions );
//     	$this->assertContainsOnly( 'string', $h->settings['hyphenationCustomExceptions'] );
//     	$this->assertArraySubset( array( 'hugo' => 'hu-go' ), $h->settings['hyphenationCustomExceptions'] );
//     	$this->assertArrayNotHasKey( 'föbaß', $h->settings['hyphenationCustomExceptions'] );
//     	$this->assertCount( 1, $h->settings['hyphenationCustomExceptions'] );
    }

    /**
     * @covers \PHP_Typography\Hyphenator::set_custom_exceptions
     */
    public function test_set_custom_exceptions_string()
    {
//     	$h = $this->h;
//     	$exceptions = "Hu-go, Fö-ba-ß";

//     	$h->set_custom_exceptions( $exceptions );
//     	$this->assertContainsOnly( 'string', $h->settings['hyphenationCustomExceptions'] );
//     	$this->assertArraySubset( array( 'hugo' => 'hu-go' ), $h->settings['hyphenationCustomExceptions'] );
//     	$this->assertArraySubset( array( 'föbaß' => 'fö-ba-ß' ), $h->settings['hyphenationCustomExceptions'] );
//     	$this->assertCount( 2, $h->settings['hyphenationCustomExceptions'] );
    }

    public function provide_hyphenate_data() {
    	return array(
    		array( 'A few words to hyphenate, like KINGdesk. Really, there should be more hyphenation here!', 'A few words to hy|phen|ate, like KING|desk. Re|ally, there should be more hy|phen|ation here!', 'en-US', true ),
    		array( 'Sauerstofffeldflasche', 'Sau|er|stoff|feld|fla|sche', 'de', true ),
    		array( 'Sauerstoff-Feldflasche', 'Sau|er|stoff-Feld|fla|sche', 'de', true ),
    		array( 'Sauerstoff-Feldflasche', 'Sauerstoff-Feldflasche', 'de', true ),
    	);
    }

    /**
     * @covers ::hyphenate
     * @covers ::hyphenation_pattern_injection
     *
     * @dataProvider provide_hyphenate_data
     */
    public function test_hyphenate( $html, $result, $lang, $hyphenate_title_case )
    {
    	$h = $this->h;
    	$h->set_language( $lang );
    	$h->set_min_length(2);
    	$h->set_min_before(2);
    	$h->set_min_after(2);
    	$h->set_custom_exceptions( array( 'KING-desk' ) );

    	$this->assertSame( $this->tokenize_sentence( $result ), $h->hyphenate( $this->tokenize_sentence( $html ), '|', $hyphenate_title_case ) );
    }

    /**
     * @covers ::hyphenate
     * @covers ::hyphenation_pattern_injection
     */
    public function test_hyphenate_wrong_encoding()
    {
    	$this->h->set_language( 'de' );
    	$this->h->set_min_length(2);
    	$this->h->set_min_before(2);
    	$this->h->set_min_after(2);

    	$tokens = $this->tokenize( mb_convert_encoding( 'Änderungsmeldung', 'ISO-8859-2' ) );
    	$hyphenated  = $this->h->hyphenate( $tokens, '|', true );
	   	$this->assertSame( $hyphenated, $tokens, 'Wrong encoding, value should be unchanged' );

	   	$tokens = $this->tokenize( 'Änderungsmeldung' );
	   	$hyphenated  = $this->h->hyphenate( $tokens, '|', true );
	   	$this->assertNotSame( $hyphenated, $tokens, 'Correct encoding, string should have been hyphenated' );
    }

    /**
     * @covers ::hyphenate
     */
    public function test_hyphenate_no_title_case()
    {
    	$this->h->set_language( 'de' );
    	$this->h->set_min_length(2);
    	$this->h->set_min_before(2);
    	$this->h->set_min_after(2);

    	$tokens = $this->tokenize( 'Änderungsmeldung' );
    	$hyphenated  = $this->h->hyphenate( $tokens, '|', false );
    	$this->assertEquals( $tokens, $hyphenated);
    }

    /**
     * @covers ::hyphenate
     */
    public function test_hyphenate_invalid()
    {
    	$this->h->set_language( 'de' );
    	$this->h->set_min_length(2);
    	$this->h->set_min_before(0);
    	$this->h->set_min_after(2);

    	$tokens = $this->tokenize( 'Änderungsmeldung' );
    	$hyphenated  = $this->h->hyphenate( $tokens );
    	$this->assertEquals( $tokens, $hyphenated);
    }

    /**
     * @covers ::hyphenate
     */
    public function test_hyphenate_no_custom_exceptions()
    {
    	$this->h->set_language( 'en-US' );
    	$this->h->set_min_length(2);
    	$this->h->set_min_before(2);
    	$this->h->set_min_after(2);

    	$this->assertTokensSame(
    		'A few words to hy|phen|ate, like KINGdesk. Re|ally, there should be more hy|phen|ation here!',
    		$this->h->hyphenate( $this->tokenize_sentence( 'A few words to hyphenate, like KINGdesk. Really, there should be more hyphenation here!' ), '|', true )
    	);
    }

    /**
     * @covers ::hyphenate
     */
    public function test_hyphenate_no_exceptions_at_all()
    {
    	$this->h->set_language( 'en-US' );
    	$this->h->set_min_length(2);
    	$this->h->set_min_before(2);
    	$this->h->set_min_after(2);
		$this->h->settings['hyphenationPatternExceptions'] = array();
		unset( $this->h->settings['hyphenationExceptions'] );

    	$this->assertSame( 'A few words to hy|phen|ate, like KINGdesk. Re|ally, there should be more hy|phen|ation here!',
    					   $this->clean_html( $this->h->hyphenate( 'A few words to hyphenate, like KINGdesk. Really, there should be more hyphenation here!' ) ) );
    }
}
