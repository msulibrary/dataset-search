'''
Kyle Hagerman
Montana State University Dataset Search

This code is based on scripts written by Jason Clark.
It is used to scrape the web for skills by MSU researchers.
Targeted websites are:
    - Google Scholar
    - ORCiD
    - MS Academic
    - ResearchGate
    - LinkedIn

INPUT: This program will read a csv list of researchers in to search for their profiles as the second command line argument.
       The csv file should contain the institution as the first item in the list.
       ARG1: python
       ARG2: script_name
       ARG3: string of what institution you are targeting
       ARG4: CSV list or string of single researcher name
       User will be prompted to enter a number indicating the file output type

OUTPUT: This program will generate csv and JSON files for each researcher.

TODO: Store data records in specified format.
Pull in csv list of researchers with institution at the beginning of the list / Prompt user of script to enter institution name?

Pass another command line argument for file type at the end.
  either JSON, CSV, or SQL Insert Statment
  one file generated for each researcher, place in directory?
  create full program menu to prompt user for each input
    series of while loops for each input
    print outcome and ask user if it's what they meant to pass to the program
    take input until they confirm it's correct

'''

#imports
import sys
import requests
import csv
import json
from bs4 import BeautifulSoup
import os.path

'''
This method handles the opening and reading of a CSV file for the express purpose of
retrieving a list of researchers.

INPUT: some string containing a filepath to a CSV file that the user input from the command line when prompted
OUTPUT: a python list of reasearchers from the CSV file, the researcher strings have spaces
'''
def readInput(filepath):
  file = open(filepath, "r")
  csv_researchers = file.read()
  researchers = csv_researchers.split(",", -1)

  for researcher in range(len(researchers)):
    researchers[researcher] = researchers[researcher].lstrip()

  return researchers

'''
This method crawls GoogleScholar with two HTTP requests.
The first request is a query containing the researcher name and the institution.
The second request is the href value of the first result from the query.

INPUT: string researcher with spaces, instutition with spaces, list of headers for the HTTP request.
OUTPUT: a list of python dictionaries containing the skills and their character lengths.
'''
def crawlGoogleScholar(researcher, institution, header_list):
  #format strings for seaching
  formatted_researcher = formatStringForSearch(researcher)
  formatted_institution = formatStringForSearch(institution)
  print("Formatted researcher: " + formatted_researcher)
  print("Formatted institution: " + formatted_institution)

  #put the search URI together
  uri = "https://scholar.google.com/citations?view_op=search_authors&mauthors=" + formatted_researcher + "+" + formatted_institution + "&hl=en&oi=ao"
  print(uri)

  #make the HTTP request for the search
  request = requests.get(uri, headers=header_list)

  #check for HTTP codes other than 200
  if request.status_code != 200:
      print('Status:', request.status_code, 'Problem with the Google Scholar search request. Exiting.')
      exit()

  #retrieve the html to sort through
  soup = BeautifulSoup(request.text, 'html.parser')

  #find the first result in the search displayed on the page
  search_results = soup.find_all(class_='gs_ai gs_scl gs_ai_chpr')
  #pull the HTML link from the search result
  try:
    URI_tail = search_results[0].a['href']

    #add the domain name for Google Scholar
    URI_head = "https://scholar.google.com/"

    #put the URI together
    uri = URI_head + URI_tail

    #make the second HTTP request for the researcher's profile page
    request = requests.get(uri, headers=header_list)

    #check for HTTP codes other than 200
    if request.status_code != 200:
        print('Status:', request.status_code, 'Problem with the Google Scholar profile request. Exiting.')
        exit()

    #get the HTML soup
    soup = BeautifulSoup(request.text, 'html.parser')

    #set these variables to generate the file names
    pageTitle = soup.title.string
    pageFileName = pageTitle.replace(' ', '-').lower()

    print ('Page Title: \n' + pageTitle)

    #set empty list for about json values
    skillList = []

    #this class may change in the future: gsc_prf_inta
    for link in soup.find_all(class_='gsc_prf_inta'):
        tagValue = link.string.strip('\r\n\t')
        print('skill data: \n' + tagValue)
        skillList.append({"skill": tagValue, "skill_length": len(tagValue)})

    #build a list to return all the values we need
    return_list = [pageTitle, pageFileName, skillList]

  except IndexError:
    print("This researcher may not have a GoogleScholar profile: " + researcher)
    print("No results were returned from the search.")

    return_list = [researcher.lower(), researcher.replace(' ', '-'.lower()), []]

  return return_list

'''

These are framework methods for adding additional sites to crawl.
The code is modular so that crawling these sites should just append to the
researcher_skills list so that WikiData can call all skills from one list.

#crawl ORCiD for skills
def crawlORCiD():
  pass

#crawl MS Academic for skills
def crawlMSAcademic():
  pass

#crawl Research Gate for skills
def crawlResearchGate():
  pass

#crawl LinkedIn for skills
def crawlLinkedIn():
  #get authorization code https://docs.microsoft.com/en-us/linkedin/shared/authentication/authorization-code-flow
  pass
'''

'''
This method takes the list of skills we have scraped from GoogleScholar (and other sites may be added)
and pulls back the URI for their machine label

INPUT: an individual skill from the list of scraped researcher skills as a string, the list of headers for an HTTP request

OUTPUT: the URI for the machine label as a string
'''
def callWikiData(skill, header_list):
  #need to build uri for query and add skill to it
  formatted_skill = formatStringForSearch(skill)
  print("Formatted Skill in WikiData call: " + formatted_skill)

  #put the search query together
  URI_head = "http://www.wikidata.org/w/api.php?action=wbgetentities&sites=enwiki&titles="
  URI_tail = "&format=json&normalize=&languages=en"
  Full_URI = URI_head + formatted_skill + URI_tail
  print("Full URI: " + Full_URI)

  #make the HTTP request
  request = requests.get(Full_URI, headers=header_list)

  #if the request did not go through properly, inform the user and cancel
  if request.status_code != 200:
      print('Status:', request.status_code, 'Problem with the WikiData request. Exiting.')
      exit()

  #get the contents and then the list of entities
  contents = request.json()
  entities = contents["entities"]

  #we just want the first entity so once its pulled we return it
  for entity in entities:
    print("The entity you have reconciled: ")
    print(entity)

    #this is the URI header with the entity we scrape from WikiData
    uri_return = "https://www.wikidata.org/wiki/" + str(entity)

    return uri_return


'''
This function formats strings by replacing spaces with "+" signs for use in URI's

INPUT: a string with multiple words separated by spaces

OUTPUT: a string with multiple words separated by plus signs
'''
def formatStringForSearch(string_with_space):
  pieces = string_with_space.split(" ")
  string_with_plus = pieces[0]
  for chunk in range(1, len(pieces)):
    string_with_plus = string_with_plus + "+" + pieces[chunk]

  return string_with_plus

#orchestrates the script
def main():
  print("\n\n\nWelcome to the MSU Dataset Search Metadata Enhancement Tool!")
  print("This tool will enrich data records in your database by matching researcher skills from Google Scholar with machine labels reconciled from WikiData.")
  print("Please follow the prompts and enter the required information.")

  #exit conditions for menu loops, each input must be confirmed
  institution_input_invalid = True
  researcher_input_type_invalid = True
  researcher_info_invalid = True
  output_type_invalid = True

  #grab the institution to search for from the user
  while(institution_input_invalid):
    print("-------------------------------------------------------------------")
    print("Please enter the institution you would like to search for: ")
    institution = input()

    print("You entered: " + institution)
    print("Is this correct? Enter yes or no.")

    response = input()

    if response == "yes" or response == "y" or response == "Yes" or response == "Y" or response == "YES":
      print("Great!")
      institution_input_invalid = False
    elif response == "no" or response == "n" or response == "No" or response == "N" or response == "NO":
      continue
    else:
      print("Your input is not recognized, please enter the institution and try again.")

  if institution == "default":
    researcher_input_type_invalid = False
    researcher_info_invalid = False
    output_type_invalid = False

    institution = "Montana State University"
    researchers = ["Jason A. Clark"]
    output_type = "JSON"

  #find out if we are searching for a single person or parsing a list of researachers
  while(researcher_input_type_invalid):
    print("-------------------------------------------------------------------")
    print("We can search for a single researcher or you may enter the file location of a CSV list of researchers.")
    print("Please enter a 1 for single researcher or a 2 for the CSV list filepath.")
    researcher_choice = input()

    print("You entered: " + researcher_choice)
    print("Is this correct? Enter yes or no.")

    response = input()

    if response == "yes" or response == "y" or response == "Yes" or response == "Y" or response == "YES":
      print("Great!")
      researcher_input_type_invalid = False
    elif response == "no" or response == "n" or response == "No" or response == "N" or response == "NO":
      continue
    else:
      print("Your input is not recognized, please try again.")

  #get the name of the single researcher
  while(researcher_info_invalid and researcher_choice == "1"):
    print("-------------------------------------------------------------------")
    print("Please enter the name of the researcher you'd like to search for: ")
    researcher_name = input()

    print("You entered: " + researcher_name)
    print("Is this correct? Enter yes or no.")

    response = input()

    if response == "yes" or response == "y" or response == "Yes" or response == "Y" or response == "YES":
      print("Great!")
      researcher_info_invalid = False
      researchers = [researcher_name]
    elif response == "no" or response == "n" or response == "No" or response == "N" or response == "NO":
      continue
    else:
      print("Your input is not recognized, please try again.")

  #get the filepath of the CSV list of researchers
  while(researcher_info_invalid and researcher_choice == "2"):
    print("-------------------------------------------------------------------")
    print("Please enter the filepath to the researchers you'd like to search for: ")
    filepath = input()

    print("You entered: " + filepath)
    print("Is this correct? Enter yes or no.")

    response = input()

    if response == "yes" or response == "y" or response == "Yes" or response == "Y" or response == "YES":
      if os.path.exists(filepath) and os.path.isfile(filepath):
        print("Great!")
        researcher_info_invalid = False
        print("Reading input, please stand by.")
        researchers = readInput(filepath)
      else:
        print("That does not appear to be a valid filepath.")
        print("Please make sure that you have provided a precise file location.")
    elif response == "no" or response == "n" or response == "No" or response == "N" or response == "NO":
      continue
    else:
      print("Your input is not recognized, please try again.")

  #will run until file output type is valid
  while(output_type_invalid):

    #prompt user for file output type
    print("Please select a file output type by number. Your choices are:")
    print("1. CSV")
    print("2. JSON")
    print("3. SQL Insert")
    #get input
    output_type = input()

    #set file output type and apply exit condition
    if output_type == "1":
      output_type_invalid = False
      output_type = "CSV"
    elif output_type == "2":
      output_type_invalid = False
      output_type = "JSON"
    elif output_type == "3":
      output_type_invalid = False
      output_type = "SQL"
    else:
      #input was not valid, loop
      print("Invalid input. Please try again.")

  print("Your list of researchers: ")
  print(researchers)
  print("*----------*")
  print("Your institution to filter by: " + institution)
  print("*----------*")

  #set the list of headers to be sent in each HTTP request
  header_list = {'User-Agent' : 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'}

  #for each researcher in the list, find their skills and reconcile with WikiData
  for researcher in range(len(researchers)):

    print("Calling Google Scholar method...")
    return_list = crawlGoogleScholar(researchers[researcher], institution, header_list)

    #parse the returned values from the list
    pageTitle = return_list[0]
    pageFileName = return_list[1]
    researcher_skills = return_list[2]

    #if we retrieved skills from GoogleScholar then make WikiData calls
    if len(researcher_skills) > 2:
      for skill in range(len(researcher_skills)):
        machine_label = callWikiData(researcher_skills[skill]['skill'], header_list)
        researcher_skills[skill]["machine_label"] = machine_label
        researcher_skills[skill]["label_length"] = len(machine_label)

      print("Skills with URI's: ")
      print(researcher_skills)
      print("*----------*")
    else:
      #put something in the researcher skills so the output file will read 'no data'
      print("No data to return")
      researcher_skills.append({'skill' : 'No Data'})

    #create a file output specific directory so as not to clutter the current folder
    cwd = os.getcwd()
    output_path = cwd + "/researchers"

    if output_type == "CSV":

      output_path += "_csv"
      try:
        os.mkdir(output_path)
      except FileExistsError:
        #folder already exists
        pass

      if not os.path.exists('./researchers_csv/'+pageTitle+'-skills.csv'):
          open('./researchers_csv/'+pageFileName+'-skills.csv', 'w').close()

      with open('./researchers_csv/'+pageFileName+'-skills.csv', 'r+') as csvFile:
          writeFile = csv.writer(csvFile)
          writeFile.writerow(researcher_skills)

    elif output_type ==  "JSON":

      output_path += "_json"
      try:
        os.mkdir(output_path)
      except FileExistsError:
        #folder already exists
        pass

      if not os.path.exists('./researchers_json/'+pageTitle+'-skills.json'):
          open('./researchers_json/'+pageFileName+'-skills.json', 'w').close()

      with open('./researchers_json/'+pageFileName+'-skills.json', 'r+') as jsonFile:
          json.dump(researcher_skills, jsonFile, indent = 4)

    elif output_type == "SQL":

      output_path += "_sql"
      try:
        os.mkdir(output_path)
      except FileExistsError:
        #folder already exists
        pass

      sql_header = "INSERT INTO `datasets` (`recordInfo_recordIdentifier`, "
      sql_middle = " VALUES ("

      if not os.path.exists('./researchers_sql/'+pageTitle + '-skills.txt'):
        txtFile = open('./researchers_sql/'+pageFileName + '-skills.txt', 'w')

      dataset_categories = ""
      dataset_category_values = ""

      try:
        for researcher_dict in range(len(researcher_skills)):
          if researcher_dict is not len(researcher_skills) - 1:
            dataset_category = "`" + "dataset_category" + str(researcher_dict + 1) + "`, "
            dataset_category_URI = "`" + "dataset_category" + str(researcher_dict + 1) + "_URI" + "`, "
            dataset_category_value = "`" + researcher_skills[researcher_dict]['skill'] + "`, "
            dataset_category_URI_value = "`" + researcher_skills[researcher_dict]['machine_label'] + "`, "
          else:
            dataset_category = "`" + "dataset_category" + str(researcher_dict + 1) + "`, "
            dataset_category_URI = "`" + "dataset_category" + str(researcher_dict + 1) + "_URI" + "`)"
            dataset_category_value = "`" + researcher_skills[researcher_dict]['skill'] + "`, "
            dataset_category_URI_value = "`" + researcher_skills[researcher_dict]['machine_label'] + "`)"

          dataset_categories += dataset_category + dataset_category_URI
          dataset_category_values += dataset_category_value + dataset_category_URI_value
        full_sql_statement = sql_header + dataset_categories + sql_middle + dataset_category_values
      except KeyError:
        full_sql_statement = "No Data"

      txtFile.write(full_sql_statement)
      txtFile.close()

if __name__ == "__main__":
  main()
