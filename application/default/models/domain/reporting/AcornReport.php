<?php
abstract class AcornReport
{
	protected static $REGULAR_FONT;
	protected static $BOLD_FONT;
	protected static $ITALIC_FONT;
	
	protected static $X_LEFT = 50;
	protected static $X_RIGHT = 570;
	protected static $Y_TOP = 750;
	
	protected static $YLOC_FOOTER = 30;
	protected static $XLOC_PAGE_FOOTER = 50;
	protected static $XLOC_DATE_FOOTER = 530;
		
	protected $yloc;
	protected $currentPage;
	protected $pages;
	
	private $reportName;
	private $pageSize;
	private $reportDirectory;
		
	public function __construct($reportName)
	{
		self::$REGULAR_FONT = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		self::$BOLD_FONT = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
		self::$ITALIC_FONT = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_ITALIC);
		
		$this->reportName = $reportName;
		$this->pageSize = Zend_Pdf_Page::SIZE_LETTER;
		$this->yloc = self::$Y_TOP;
		$this->pages = array();
		
		//$fc = Zend_Controller_Front::getInstance();
       // $initializer = $fc->getPlugin('Initializer');
        $config = Zend_Registry::getInstance()->get(ACORNConstants::CONFIG_NAME);
        $this->reportDirectory = $config->getReportsDirectory() . '/pdfreports';
	}
	
	public function setDefaultPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
		if ($pageSize == Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE)
		{
			self::$Y_TOP = 570;
			self::$X_RIGHT = 750;
			self::$XLOC_DATE_FOOTER = 710;
		}
		else
		{
			self::$Y_TOP = 750;
			self::$X_RIGHT = 570;
			self::$XLOC_DATE_FOOTER = 530;
		}
	}
	
	public function drawReport()
	{
		ini_set("memory_limit","256M");
		$date = date(ACORNConstants::$DATE_FILENAME_FORMAT);
		$filename = $this->reportName . '_' . $date;
		//Strip the name of spaces, slashes and commas to create a filename
		$fullfilename = $this->reportDirectory . '/' . $filename . '.pdf';

		$pdf = new Zend_Pdf();

		//612 x 792
		$this->currentPage = new Zend_Pdf_Page($this->pageSize);
		$this->currentPage->setFont(self::$REGULAR_FONT, 8);
		
		$this->drawMainHeader();
			
		try 
		{
			$this->drawReportTitle($this->yloc);
			$this->drawReportBody();
		}
		catch (Exception $e)
		{
			Logger::log($e->getMessage(), Zend_Log::ERR);
			$this->currentPage->drawText('There was a problem creating this PDF.  Please notify the ACORN administrator.', 150, $this->yloc);
		}
		
		array_push($this->pages, $this->currentPage);
		
		$this->drawFooter();
		
		$pdf->pages = $this->pages;
		$pdf->save($fullfilename);
		ini_set("memory_limit","128M");
		return $filename . '.pdf';
	}
	
	protected function drawMainHeader()
	{
		//Catch an exception that the image my throw.
		try 
		{
			$image = Zend_Pdf_Image::imageWithPath('images/Veritas.jpg');
			Logger::log($image->getProperties());
			$this->currentPage->drawImage($image, self::$X_LEFT, self::$Y_TOP-50, self::$X_LEFT + 70, self::$Y_TOP+15);
		}
		catch (Exception $e)
		{
			Logger::log($e->getMessage(), Zend_Log::ERR);
		}
		$this->currentPage->setFont(self::$REGULAR_FONT, 12);
		$title = strtoupper('HARVARD LIBRARY');
		$length = strlen($title);
		//Attempt to calculate the center of the page based on a font of 12
		//Some minor calculations estimates that the following will 
		//target the center of the page.
		$xloc = $length / .275;
		$xloc = $this->currentPage->getWidth()/2 - round($xloc, 0);
		$this->currentPage->drawText('HARVARD LIBRARY', $xloc, self::$Y_TOP);
		$title = strtoupper('WEISSMAN PRESERVATION CENTER');
		$length = strlen($title);
		//Attempt to calculate the center of the page based on a font of 12
		//Some minor calculations estimates that the following will 
		//target the center of the page.
		$xloc = $length / .275;
		$xloc = $this->currentPage->getWidth()/2 - round($xloc, 0);
		$this->currentPage->drawText('WEISSMAN PRESERVATION CENTER', $xloc, self::$Y_TOP-20);
		$this->yloc = self::$Y_TOP - 60;
	}
	
	/*
	 * Draws the report title using upper case letters.
	 */
	protected function drawReportTitle($yloc)
	{
		$title = $this->getReportTitle();
		if (!is_array($title))
		{	
			$title = strtoupper($title);
			$length = strlen($title);
			//Attempt to calculate the center of the page based on a font of 12
			//Some minor calculations estimates that the following will 
			//target the center of the page.
			$xloc = $length / .275;
			$xloc = $this->currentPage->getWidth()/2 - round($xloc, 0);
			$this->currentPage->setFont(self::$BOLD_FONT, 12);
			$this->currentPage->drawText($title, $xloc, $yloc);
			$this->yloc = $yloc - 20;
		}
		else 
		{
			foreach ($title as $titleobject)
			{
				$title = strtoupper($titleobject);
				$length = strlen($titleobject);
				//Attempt to calculate the center of the page based on a font of 12
				//Some minor calculations estimates that the following will 
				//target the center of the page.
				$xloc = $length / .275;
				$xloc = $this->currentPage->getWidth()/2 - round($xloc, 0);
				$this->currentPage->setFont(self::$BOLD_FONT, 12);
				$this->currentPage->drawText($title, $xloc, $yloc);
				$yloc -= 15;
			}
			$this->yloc = $yloc - 35;
		}
	}
	
	
	protected function checkYLoc($minvalue = 70, $pagetype = Zend_Pdf_Page::SIZE_LETTER, $yloc = 750)
	{
		if ($this->yloc <= $minvalue)
		{
			$font = $this->currentPage->getFont();
			$fontsize = $this->currentPage->getFontSize();
			
			if ($pagetype == Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE && $yloc == 750)
			{
				$yloc = 570;
			}
			
			array_push($this->pages, $this->currentPage);
			$this->currentPage = new Zend_Pdf_Page($pagetype);
			$this->currentPage->setFont($font, $fontsize);
			$this->yloc = $yloc;
			return TRUE;
		}
		return FALSE;
	}
	
	private function isUrl($url)
	{
	    $info = parse_url($url);
	    return ($info['scheme']=='http'||$info['scheme']=='https')&&$info['host']!="";
	} 
	
	protected function createWordWrap($sentencetowrap, $xloc, $maxlen = 30, $yspacing = 10, $checkyloc = FALSE, $drawReportTitle = TRUE)
	{
		$tempyloc = $this->yloc;
		$delimiter = "";
		$wraparray = explode(" ", $sentencetowrap);
		$sentence = "";
		$words = array();
		//Break up the word into smaller chunks
		foreach($wraparray as $word)
		{
			//If the URL is longer than the allowed maxlen,
			//then push the words onto the stack and break up
			//the URL to fit on the page.
			if (strlen($word) >= $maxlen && $this->isUrl($word))
			{
				array_push($words, $sentence);
				
				$remainder = $word;
				
				while (!empty($remainder))
				{
					if(strlen($remainder) >= $maxlen)
					{
						$sentence = substr($remainder, 0, $maxlen);
						$remainder = substr($remainder, $maxlen);
					}
					else 
					{
						$sentence = $remainder;
						$remainder = "";
					}
					array_push($words, $sentence);
				}
				$sentence = "";
				$delimiter = "";
			}
			//If it isn't a URL, proceed normally
			else
			{
				$tempsentence = $sentence . $delimiter . $word;
				if (strlen($tempsentence) >= $maxlen)
				{
					array_push($words, $sentence);
					$sentence = $word;
				}
				else 
				{
					$sentence = $sentence . $delimiter . $word;
				}
				$delimiter = " ";
			}
		}
		//Get the last sentence.
		array_push($words, $sentence);
		
		//Print out the chunks
		foreach($words as $word)
		{
			$this->currentPage->drawText($word, $xloc, $this->yloc, 'UTF-8');
			if ($checkyloc && $this->checkYLoc(70, $this->pageSize))
			{
				$font = $this->currentPage->getFont();
				$fontsize = $this->currentPage->getFontSize();
				if ($drawReportTitle)
				{
					$this->drawReportTitle($this->yloc);
				}
				$this->currentPage->setFont($font, $fontsize);
			}
			else 
			{
				$this->yloc -= $yspacing;
			}
		}
		$yloc = $this->yloc;
		$this->yloc = $tempyloc;
		return $yloc;
	}
	
	protected function parseAndDrawNewlineTextBlock($texttoparse, $xloc, $wraptext = TRUE, $maxlen = 30, $yspacing = 10, $checkyloc = FALSE)
	{
		$tempyloc = $this->yloc;
		$textarray = explode("\n", $texttoparse);
		//Break up the word into smaller chunks
		foreach($textarray as $sentence)
		{
			if ($wraptext)
			{
				$this->yloc = $this->createWordWrap($sentence, $xloc, $maxlen, $yspacing, $checkyloc);
				
			}
			else 
			{
				$this->currentPage->drawText($sentence, $xloc, $this->yloc);
				if ($checkyloc && $this->checkYLoc())
				{
					$this->drawReportTitle($this->yloc);
					$this->currentPage->setFont(self::$REGULAR_FONT, 10);
				}
				else 
				{
					$this->yloc -= $yspacing;
				}
			}
		}
		$yloc = $this->yloc;
		$this->yloc = $tempyloc;
		return $yloc;
	}
	
	protected function drawFooter()
	{
		$footer = $this->getCustomFooter();
		$length = strlen($footer);
		//Attempt to calculate the center of the page based on a font of 8
		//Some minor calculations estimates that the following will 
		//target the center of the page.
		$customxloc = $length * 2;
		$customxloc = $this->currentPage->getWidth()/2 - round($customxloc, 0);
		
		$count = count($this->pages);
		$counter = 1;
		foreach ($this->pages as $page)
		{
			$zenddate = new Zend_Date();
			$date = $zenddate->toString(ACORNConstants::$ZEND_DATE_FORMAT);
			$page->setFont(self::$REGULAR_FONT, 8);
			$page->drawText('Page ' . $counter . ' of ' . $count, self::$XLOC_PAGE_FOOTER, self::$YLOC_FOOTER);
			$page->drawText($footer, $customxloc, self::$YLOC_FOOTER);
			$page->drawText($date, self::$XLOC_DATE_FOOTER, self::$YLOC_FOOTER);
			$counter++;
		}
	}
	
	/*
	 * Wraps the call numbers and returns an array of values.
	 * 
	 * @return array - 'yloc' = terminating yloc; 'newpages' = number of new pages created.
	 */
	protected function createCallNumberWordWrap($sentencetowrap, $xloc, $maxlen = 30, $yspacing = 10, $checkyloc = FALSE, $drawReportTitle = TRUE)
	{
		$tempyloc = $this->yloc;
		$delimiter = "";
		$wraparray = explode(" ", $sentencetowrap);
		$sentence = "";
		$words = array();
		$newpages = 0;
		//Break up the word into smaller chunks
		foreach($wraparray as $word)
		{
			$tempsentence = $sentence . $delimiter . $word;
			if (strlen($tempsentence) >= $maxlen)
			{
				array_push($words, $sentence);
				$sentence = $word;
			}
			else 
			{
				$sentence = $sentence . $delimiter . $word;
			}
			$delimiter = " ";
		}
		//Get the last sentence.
		array_push($words, $sentence);
		
		//Print out the chunks
		foreach($words as $word)
		{
			$this->currentPage->drawText($word, $xloc, $this->yloc, 'UTF-8');
			if ($checkyloc && $this->checkYLoc(70, $this->pageSize, self::$Y_TOP-20))
			{
				$font = $this->currentPage->getFont();
				$fontsize = $this->currentPage->getFontSize();
				if ($drawReportTitle)
				{
					$this->drawReportTitle($this->yloc);
				}
				$this->currentPage->setFont($font, $fontsize);
				$newpages++;
			}
			else 
			{
				$this->yloc -= $yspacing;
			}
		}
		$yloc = $this->yloc;
		$this->yloc = $tempyloc;
		return array('yloc' => $yloc, 'newpages' => $newpages);
	}
	
	
	
	protected abstract function drawReportBody();
	/*
	 * Get the title for the report.  
	 * @return mixed - string or array of strings
	 */
	protected abstract function getReportTitle();
	protected abstract function getCustomFooter();
}
?>