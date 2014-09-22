<?php

include_once "SCMDataRegistry.php";

class SCMCommitRegistry extends SCMDataRegistry
{
	public function addCommit( $version, $date, $text, $author )
	{
		$this->addData( array (
				'Version' => $version,
				'RecordModified' => strftime('%Y/%m/%d %H:%M:%S',strtotime($date)),
				'CommitDate' => strftime('%Y/%m/%d %H:%M:%S',strtotime($date)),
				'Comment' => IteratorBase::utf8towin($text),
				'Description' => IteratorBase::utf8towin($text),
				'Author' => IteratorBase::utf8towin($author)
		));
	}
}