# MSUDSWebCrawling
MSUDS Web Crawling Python Scripts to retrieve skills from specifically targeted websites

Run the 'metadata_enhancement.py' script to be prompted with a user menu on the command prompt.
Enter your institution and confirm it's correctness.
Enter your search criteria. 
 - This program will accept single researcher names or a CSV list of researchers).
Enter the researcher name OR the relative filepath to the CSV list of researchers. 
 - It is recommended that you place the CSV list inside the current working directory of the script, then you only have to specify the file name.
Enter the output file type
 - Either CSV, JSON, or a SQL statement that will be placed in a .txt file
 
The script will then run and gather what data it can from Google Scholar
 - Other sites may be added for crawling later (ORCiD, MSAcademic, LinkedIn, ResearchGate...)
 
A new directory will be added based on the file output type chosen and each researcher will have a results file generated.
At a glance, capitalized files have no data returned (No Google Scholar profile exists or was found). Lower case files will have data returned.
Should a WikiData entry for a skill not be found the data is still saved but the machine_label URI will end in a '-1'. This indicates that the skill retrieved may need to be humanly parsed to receive an accurate machine_label.

Note there are other scripts in this repo. They have been used to crawl their respective sites in the past but are outdated and need to be refreshed. 
