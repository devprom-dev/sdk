<?php

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class CheckQuestionIterator extends OrderedIterator
 {
 	function getDisplayName()
 	{
 		$lang_id = getLanguageId();
 		
 		switch ( $lang_id )
 		{
 			case 1:
 				return $this->get('QuestionRussian');

 			case 2:
 				return $this->get('QuestionEnglish');
 		}
 	}
 	
 	function getAnswer()
 	{
 		$lang_id = getLanguageId();
 		
 		switch ( $lang_id )
 		{
 			case 1:
 				return $this->get('Answer');

 			case 2:
 				return $this->get('AnswerEnglish');
 		}
 	}

 	function getHash()
 	{
 		return $this->object->getHash($this->getAnswer());
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class NumberQuestionIterator extends CheckQuestionIterator
 {
 	var $generated;
 	
 	function NumberQuestionIterator( $generated )
 	{
 		$this->generated = $generated; 
 	}
 	
 	function getDisplayName()
 	{
 		return translate('¬ведите число').' '.$this->generated;
 	}
 	
 	function getAnswer()
 	{
 		return $this->generated;
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class CheckQuestion extends Metaobject
 {
 	function CheckQuestion() 
 	{
 		parent::Metaobject('cms_CheckQuestion');
		$this->defaultsort = 'RecordCreated DESC';
 	}
 	
 	function createIterator() 
 	{
 		return new CheckQuestionIterator( $this );
 	}
 	
	function getPage() {
		return 'questions.php?';
	}
	
	function getRandom()
	{
		srand();
		$question_id = rand(1, 50);
		
		$it = $this->getExact($question_id);
		
		if ( $it->count() < 1 )
		{
			$it = new NumberQuestionIterator( rand(1, 999) );
			$it->object = $this;
			
			return $it;
		}
		else
		{
			return $it;
		}
	}
	
 	function getHash( $answer = '' )
 	{
 		// the hash is valid during one hour
 		return md5(INSTALLATION_UID.trim(strtolower($answer)).date('%Y.%d.%m.%H'));
 	}

	function checkAnswer( $hash_value, $answer )
	{
		return $this->getHash( $answer ) == $hash_value;
	}
 }

 ?>
