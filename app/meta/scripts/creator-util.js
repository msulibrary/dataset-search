/*
 * Iterate through the creators array creating new html content for the creatorDiv element.
 *
 * The creators array is a three-dimensional array with the following structure:
 *
 *     |    0     |       1       |       2       |
 *     +----------+---------------+---------------+
 *   0 | Creator 1 | Affiliation 1 | Affiliation 2 |
 *     +----------+---------------+---------------+
 *   1 | Creator 2 | Affiliation 1 | Affiliation 2 |
 *     +----------+---------------+---------------+
 *   2 | Creator 3 | Affiliation 1 | Affiliation 2 |
 *     +----------+---------------+---------------+
 *
 *
 *               |  0   |   1   |  2   |  3  |       4       |
 *               +------+-------+------+-----+---------------+
 *       Creator | Name | ORCiD | Type | URL | Contact Point |
 *               +------+-------+------+-----+---------------+
 *
 *
 *               |    0    |     1      |        2          |
 *               +---------+------------+-------------------+
 *   Affiliation | College | Department | Other Affiliation |
 *               +---------+------------+-------------------+
 *
 *
 * Note:  creators[i][0][0] = Creator Name
 *        creators[i][0][1] = Creator Type
 *        creators[i][0][2] = Creator Type
 *        creators[i][0][3] = Creator URL
 *        creators[i][0][4] = Creator Contact Point
 *        creators[i][j][0] = MSU College where j > 0
 *        creators[i][j][1] = MSU Department where j > 0
 *        creators[i][j][2] = Other Affiliation where j > 0
 *
 */
function displayCreators() {
	var creatorDiv = "";
	for (i = 0; i < creators.length; i++) {
		creatorDiv += ("<fieldset><legend>Creator " + (i + 1) + "</legend>\n");
		creatorDiv += ("<h3><label for=\"creator_name\" title=\"creator_name\">Creator " + (i + 1) + " Name&nbsp;&nbsp;(Last, First Middle [or Middle Initial] )</label></h3>\n");
		creatorDiv += ("<input class=\"text\" type=\"text\" id=\"creator_name\" name=\"creator_name" + i + "\" size=\"40\" maxlength=\"255\" value=\"" + creators[i][0][0] + "\"/>\n");
		creatorDiv += ("<h3><label for=\"creator_orcid\" title=\"creator_orcid\">Creator " + (i + 1) + " ORCiD</label></h3>\n");
		creatorDiv += ("<input class=\"text\" type=\"text\" id=\"creator_orcid\" name=\"creator_orcid" + i + "\" size=\"40\" maxlength=\"255\" value=\"" + creators[i][0][1] + "\"/>\n");

		creatorDiv += ("<h3><label for=\"creator_type\" title=\"creator_type\">Creator " + (i + 1) + " Type</label></h3>\n");
//		creatorDiv += ("<input class=\"text\" type=\"text\" id=\"creator_type\" name=\"creator_type" + i + "\" size=\"40\" maxlength=\"255\" value=\"" + creators[i][0][2] + "\"/>\n");
		creatorDiv += ("<select id=\"creator_type\" name=\"creator_type" + i + "\" size=\"1\" required>\n");
		creatorDiv += ("  <option value=\"\"></option>\n");
		creatorDiv += ("  <option value=\"person\"");
		if (creators[i][0][2] == "person")
		{
			creatorDiv += (" selected");
		}
		creatorDiv += (">person</option>\n");
		creatorDiv += ("  <option value=\"organization\"");
		if (creators[i][0][2] == "organization")
		{
			creatorDiv += (" selected");
		}
		creatorDiv += (">organization</option>\n");
		creatorDiv += ("  <option value=\"community\"");
		if (creators[i][0][2] == "community")
		{
			creatorDiv += (" selected");
		}
		creatorDiv += (">community</option>\n");
		creatorDiv += ("  <option value=\"center\"");
		if (creators[i][0][2] == "center")
		{
			creatorDiv += (" selected");
		}
		creatorDiv += (">center</option>\n");
		creatorDiv += ("  <option value=\"department\"");
		if (creators[i][0][2] == "department")
		{
			creatorDiv += (" selected");
		}
		creatorDiv += (">department</option>\n");
		creatorDiv += ("</select>\n");

		creatorDiv += ("<h3><label for=\"creator_url\" title=\"creator_url\">Creator " + (i + 1) + " URL</label></h3>\n");
		creatorDiv += ("<input class=\"text\" type=\"text\" id=\"creator_url\" name=\"creator_url" + i + "\" size=\"40\" maxlength=\"255\" value=\"" + creators[i][0][3] + "\"/>\n");
		creatorDiv += ("<h3><label for=\"creator_contactPoint\" title=\"creator_contactPoint\">Creator " + (i + 1) + " Contact Point</label></h3>\n");
		creatorDiv += ("<input class=\"text\" type=\"text\" id=\"creator_contactPoint\" name=\"creator_contactPoint" + i + "\" size=\"40\" maxlength=\"255\" value=\"" + creators[i][0][4] + "\"/>\n");
		creatorDiv += ("&nbsp;&nbsp;<input class=\"submit\" type=\"button\" onClick=\"removeCreator(" + i + ");\" value=\"Remove Creator\" />\n");
		creatorDiv += ("<h3><input class=\"submit\" type=\"button\" onClick=\"addAffiliation(" + i  + ");\" value=\"Add Affiliation\"/></h3>\n\n");

		for (j = 1; j < creators[i].length; j++) {
			creatorDiv += ("<fieldset><legend class=\"affiliation\">Creator " + (i + 1) + " Affiliation " + j + "</legend>\n");

			creatorDiv += ("<h3><input class=\"submit\" type=\"button\" onClick=\"removeAffiliation(" + i + ", " + j + ");\" value=\"Remove Affiliation\"/></h3>\n");

			creatorDiv += ("<h3><label for=\"affiliation\" title=\"name_affiliation_msuCollege\">MSU College</label></h3>\n");
			creatorDiv += ("<select id=\"affiliation\" name=\"affiliation" + i + "-" + j + "-0\" size=\"1\">\n");
				creatorDiv += ("  <option value=\"\"></option>\n");
				creatorDiv += ("  <option value=\"Agriculture\"");
				if (creators[i][j][0] == "Agriculture")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Agriculture</option>\n");
				creatorDiv += ("  <option value=\"Arts & Architecture\"");
				if (creators[i][j][0] == "Arts & Architecture")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Arts & Architecture</option>\n");
				creatorDiv += ("  <option value=\"Jake Jabs College of Business\"");
				if (creators[i][j][0] == "Jake Jabs College of Business")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Jake Jabs College of Business</option>\n");
				creatorDiv += ("  <option value=\"Education, Health & Human Development\"");
				if (creators[i][j][0] == "Education, Health & Human Development")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Education, Health & Human Development</option>\n");
				creatorDiv += ("  <option value=\"Gallatin College\"");
				if (creators[i][j][0] == "Gallatin College")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Gallatin College</option>\n");
				creatorDiv += ("  <option value=\"Honors College\"");
				if (creators[i][j][0] == "Honors College")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Honors College</option>\n");
				creatorDiv += ("  <option value=\"Letters & Science\"");
				if (creators[i][j][0] == "Letters & Science")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Letters & Science</option>\n");
				creatorDiv += ("  <option value=\"Norm Asbjornson College of Engineering\"");
				if (creators[i][j][0] == "Norm Asbjornson College of Engineering")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Norm Asbjornson College of Engineering</option>\n");
				
				creatorDiv += ("  <option value=\"Nursing\"");
				if (creators[i][j][0] == "Nursing")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Nursing</option>\n");
				creatorDiv += ("  <option value=\"Library\"");
				if (creators[i][j][0] == "Library")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Library</option>\n");
				creatorDiv += ("  <option value=\"University College\"");
				if (creators[i][j][0] == "University College")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">University College</option>\n");
				creatorDiv += ("</select>\n");
   			
			creatorDiv += ("<h3><label for=\"affiliation\" title=\"name_affiliation_msuCollege_abbr\">MSU College Abbreviation</label></h3>\n");
			creatorDiv += ("<select id=\"affiliation\" name=\"affiliation" + i + "-" + j + "-1\" size=\"1\">\n");
				creatorDiv += ("  <option value=\"\"></option>\n");
				creatorDiv += ("  <option value=\"AG\"");
				if (creators[i][j][1] == "AG")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">AG</option>\n");
				creatorDiv += ("  <option value=\"AA\"");
				if (creators[i][j][1] == "AA")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">AA</option>\n");
				creatorDiv += ("  <option value=\"BU\"");
				if (creators[i][j][1] == "BU")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">BU</option>\n");
				creatorDiv += ("  <option value=\"ED\"");
				if (creators[i][j][1] == "ED")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">ED</option>\n");
				creatorDiv += ("  <option value=\"GC\"");
				if (creators[i][j][1] == "GC")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">GC</option>\n");
				creatorDiv += ("  <option value=\"HC\"");
				if (creators[i][j][1] == "HC")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">HC</option>\n");
				creatorDiv += ("  <option value=\"LS\"");
				if (creators[i][j][1] == "LS")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">LS</option>\n");
				creatorDiv += ("  <option value=\"EN\"");
				if (creators[i][j][1] == "EN")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">EN</option>\n");
				creatorDiv += ("  <option value=\"NU\"");
				if (creators[i][j][1] == "NU")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">NU</option>\n");
				creatorDiv += ("  <option value=\"LB\"");
				if (creators[i][j][1] == "LB")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">LB</option>\n");
				creatorDiv += ("  <option value=\"UC\"");
				if (creators[i][j][1] == "UC")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">UC</option>\n");
				creatorDiv += ("</select>\n");

			creatorDiv += ("<h3><label for=\"affiliation\" title=\"name_affiliation_msuDepartment\">MSU Department</label></h3>\n");
			creatorDiv += ("<select id=\"affiliation\" name=\"affiliation" + i + "-" + j + "-2\" size=\"1\">\n");
				creatorDiv += ("  <option value=\"\"></option>\n");
				creatorDiv += ("  <option value=\"Agricultural & Technology Education\"");
				if (creators[i][j][2] == "Agricultural & Technology Education")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Agricultural & Technology Education</option>\n");
				creatorDiv += ("  <option value=\"Agricultural Economics & Economics\"");
				if (creators[i][j][2] == "Agricultural Economics & Economics")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Agricultural Economics & Economics</option>\n");
				creatorDiv += ("  <option value=\"American Studies\"");
				if (creators[i][j][2] == "American Studies")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">American Studies</option>\n");
				creatorDiv += ("  <option value=\"Animal & Range Science\"");
				if (creators[i][j][2] == "Animal & Range Science")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Animal & Range Science</option>\n");
				creatorDiv += ("  <option value=\"Architecture\"");
				if (creators[i][j][2] == "Architecture")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Architecture</option>\n");
				creatorDiv += ("  <option value=\"Art\"");
				if (creators[i][j][2] == "Art")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Art</option>\n");
				creatorDiv += ("  <option value=\"Business\"");
				if (creators[i][j][2] == "Business")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Business</option>\n");
				creatorDiv += ("  <option value=\"Chemical & Biological Engineering\"");
				if (creators[i][j][2] == "Chemical & Biological Engineering")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Chemical & Biological Engineering</option>\n");
				creatorDiv += ("  <option value=\"Chemistry & Biochemistry\"");
				if (creators[i][j][2] == "Chemistry & Biochemistry")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Chemistry & Biochemistry</option>\n");
				creatorDiv += ("  <option value=\"Civil Engineering\"");
				if (creators[i][j][2] == "Civil Engineering")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Civil Engineering</option>\n");
				creatorDiv += ("  <option value=\"Earth Sciences\"");
				if (creators[i][j][2] == "Earth Sciences")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Earth Sciences</option>\n");
				creatorDiv += ("  <option value=\"Ecology\"");
				if (creators[i][j][2] == "Ecology")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Ecology</option>\n");
				creatorDiv += ("  <option value=\"Education\"");
				if (creators[i][j][2] == "Education")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Education</option>\n");
				creatorDiv += ("  <option value=\"Electrical & Computer Engineering\"");
				if (creators[i][j][2] == "Electrical & Computer Engineering")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Electrical & Computer Engineering</option>\n");
				creatorDiv += ("  <option value=\"English\"");
				if (creators[i][j][2] == "English")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">English</option>\n");
				creatorDiv += ("  <option value=\"Film & Photography\"");
				if (creators[i][j][2] == "Film & Photography")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Film & Photography</option>\n");
				creatorDiv += ("  <option value=\"Gallatin College Programs\"");
				if (creators[i][j][2] == "Gallatin College Programs")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Gallatin College Programs</option>\n");
				creatorDiv += ("  <option value=\"Gianforte School of Computing\"");
				if (creators[i][j][2] == "Gianforte School of Computing")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Gianforte School of Computing</option>\n");
				creatorDiv += ("  <option value=\"Health & Human Development\"");
				if (creators[i][j][2] == "Health & Human Development")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Health & Human Development</option>\n");
				creatorDiv += ("  <option value=\"Health Professions Advising\"");
				if (creators[i][j][2] == "Health Professions Advising")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Health Professions Advising</option>\n");
				creatorDiv += ("  <option value=\"History & Philosophy\"");
				if (creators[i][j][2] == "History & Philosophy")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">History & Philosophy</option>\n");
				creatorDiv += ("  <option value=\"Honors\"");
				if (creators[i][j][2] == "Honors")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Honors</option>\n");
				creatorDiv += ("  <option value=\"Intercollege Programs for Science Education\"");
				if (creators[i][j][2] == "Intercollege Programs for Science Education")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Intercollege Programs for Science Education</option>\n");
				creatorDiv += ("  <option value=\"Land Resources & Environmental Sciences\"");
				if (creators[i][j][2] == "Land Resources & Environmental Sciences")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Land Resources & Environmental Sciences</option>\n");
				creatorDiv += ("  <option value=\"Liberal Studies\"");
				if (creators[i][j][2] == "Liberal Studies")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Liberal Studies</option>\n");
				creatorDiv += ("  <option value=\"Library\"");
				if (creators[i][j][2] == "Library")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Library</option>\n");
				creatorDiv += ("  <option value=\"Mathematical Sciences\"");
				if (creators[i][j][2] == "Mathematical Sciences")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Mathematical Sciences</option>\n");
				creatorDiv += ("  <option value=\"Mechnical & Industrial Engineering\"");
				if (creators[i][j][2] == "Mechnical & Industrial Engineering")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Mechnical & Industrial Engineering</option>\n");
				creatorDiv += ("  <option value=\"Microbiology & Immunology\"");
				if (creators[i][j][2] == "Microbiology & Immunology")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Microbiology & Immunology</option>\n");
				creatorDiv += ("  <option value=\"Modern Languages & Literatures\"");
				if (creators[i][j][2] == "Modern Languages & Literatures")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Modern Languages & Literatures</option>\n");
				creatorDiv += ("  <option value=\"Music\"");
				if (creators[i][j][2] == "Music")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Music</option>\n");
				creatorDiv += ("  <option value=\"Native American Studies\"");
				if (creators[i][j][2] == "Native American Studies")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Native American Studies</option>\n");
				creatorDiv += ("  <option value=\"Nursing\"");
				if (creators[i][j][2] == "Nursing")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Nursing</option>\n");
				creatorDiv += ("  <option value=\"Physics\"");
				if (creators[i][j][2] == "Physics")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Physics</option>\n");
				creatorDiv += ("  <option value=\"Plant Sciences & Plant Pathology\"");
				if (creators[i][j][2] == "Plant Sciences & Plant Pathology")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Plant Sciences & Plant Pathology</option>\n");
				creatorDiv += ("  <option value=\"Political Science\"");
				if (creators[i][j][2] == "Political Science")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Political Science</option>\n");
				creatorDiv += ("  <option value=\"Psychology\"");
				if (creators[i][j][2] == "Psychology")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Psychology</option>\n");
				creatorDiv += ("  <option value=\"Research Centers\"");
				if (creators[i][j][2] == "Research Centers")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Research Centers</option>\n");
				creatorDiv += ("  <option value=\"Sociology & Anthropology\"");
				if (creators[i][j][2] == "Sociology & Anthropology")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Sociology & Anthropology</option>\n");
				creatorDiv += ("  <option value=\"University Studies\"");
				if (creators[i][j][2] == "University Studies")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">University Studies</option>\n");
				creatorDiv += ("  <option value=\"Workforce Program\"");
				if (creators[i][j][2] == "Workforce Program")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">Workforce Program</option>\n");
				creatorDiv += ("</select>\n");

			creatorDiv += ("<h3><label for=\"affiliation\" title=\"name_affiliation_msuDepartment_abbr\">MSU Department Abbreviation</label></h3>\n");
			creatorDiv += ("<select id=\"affiliation\" name=\"affiliation" + i + "-" + j + "-3\" size=\"1\">\n");
				creatorDiv += ("  <option value=\"\"></option>\n");
				creatorDiv += ("  <option value=\"A&TE\"");
				if (creators[i][j][3] == "A&TE")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">A&TE</option>\n");
				creatorDiv += ("  <option value=\"AGEC\"");
				if (creators[i][j][3] == "AGEC")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">AGEC</option>\n");
				creatorDiv += ("  <option value=\"AS\"");
				if (creators[i][j][3] == "AS")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">AS</option>\n");
				creatorDiv += ("  <option value=\"ANRS\"");
				if (creators[i][j][3] == "ANRS")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">ANRS</option>\n");
				creatorDiv += ("  <option value=\"ARCH\"");
				if (creators[i][j][3] == "ARCH")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">ARCH</option>\n");
				creatorDiv += ("  <option value=\"ART\"");
				if (creators[i][j][3] == "ART")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">ART</option>\n");
				creatorDiv += ("  <option value=\"BUS\"");
				if (creators[i][j][3] == "BUS")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">CBU</option>\n");
				creatorDiv += ("  <option value=\"CHBE\"");
				if (creators[i][j][3] == "CHBE")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">CHBE</option>\n");
				creatorDiv += ("  <option value=\"CHEM\"");
				if (creators[i][j][3] == "CHEM")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">CHEM</option>\n");
				creatorDiv += ("  <option value=\"CE\"");
				if (creators[i][j][3] == "CE")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">CE</option>\n");
				creatorDiv += ("  <option value=\"ESCI\"");
				if (creators[i][j][3] == "ESCI")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">ESCI</option>\n");
				creatorDiv += ("  <option value=\"ECOL\"");
				if (creators[i][j][3] == "ECOL")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">ECOL</option>\n");
				creatorDiv += ("  <option value=\"EDUC\"");
				if (creators[i][j][3] == "EDUC")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">EDUC</option>\n");
				creatorDiv += ("  <option value=\"ECE\"");
				if (creators[i][j][3] == "ECE")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">ECE</option>\n");
				creatorDiv += ("  <option value=\"ENGL\"");
				if (creators[i][j][3] == "ENGL")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">ENGL</option>\n");
				creatorDiv += ("  <option value=\"GCP\"");
				if (creators[i][j][3] == "GCP")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">GCP</option>\n");
				creatorDiv += ("  <option value=\"SFP\"");
				if (creators[i][j][3] == "SFP")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">SFP</option>\n");
				creatorDiv += ("  <option value=\"CS\"");
				if (creators[i][j][3] == "CS")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">CS</option>\n");
				creatorDiv += ("  <option value=\"HHD\"");
				if (creators[i][j][3] == "HHD")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">HHD</option>\n");
				creatorDiv += ("  <option value=\"HPA\"");
				if (creators[i][j][3] == "HPA")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">HPA</option>\n");
				creatorDiv += ("  <option value=\"HIST\"");
				if (creators[i][j][3] == "HIST")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">HIST</option>\n");
				creatorDiv += ("  <option value=\"HONR\"");
				if (creators[i][j][3] == "HONR")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">HONR</option>\n");
				creatorDiv += ("  <option value=\"IPSE\"");
				if (creators[i][j][3] == "IPSE")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">IPSE</option>\n");
				creatorDiv += ("  <option value=\"LRES\"");
				if (creators[i][j][3] == "LRES")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">LRES</option>\n");
				creatorDiv += ("  <option value=\"LS\"");
				if (creators[i][j][3] == "LS")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">LS</option>\n");
				creatorDiv += ("  <option value=\"LSCI\"");
				if (creators[i][j][3] == "LSCI")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">LSCI</option>\n");
				creatorDiv += ("  <option value=\"MATH\"");
				if (creators[i][j][3] == "MATH")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">MATH</option>\n");
				creatorDiv += ("  <option value=\"MIE\"");
				if (creators[i][j][3] == "MIE")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">MIE</option>\n");
				creatorDiv += ("  <option value=\"MBII\"");
				if (creators[i][j][3] == "MBII")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">MBII</option>\n");
				creatorDiv += ("  <option value=\"ML\"");
				if (creators[i][j][3] == "ML")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">ML</option>\n");
				creatorDiv += ("  <option value=\"MUS\"");
				if (creators[i][j][3] == "MUS")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">MUS</option>\n");
				creatorDiv += ("  <option value=\"NAS\"");
				if (creators[i][j][3] == "NAS")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">NAS</option>\n");
				creatorDiv += ("  <option value=\"CNU\"");
				if (creators[i][j][3] == "CNU")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">CNU</option>\n");
				creatorDiv += ("  <option value=\"PHYS\"");
				if (creators[i][j][3] == "PHYS")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">PHYS</option>\n");
				creatorDiv += ("  <option value=\"PLS\"");
				if (creators[i][j][3] == "PLS")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">PLS</option>\n");
				creatorDiv += ("  <option value=\"POLS\"");
				if (creators[i][j][3] == "POLS")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">POLS</option>\n");
				creatorDiv += ("  <option value=\"PSY\"");
				if (creators[i][j][3] == "PSY")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">PSY</option>\n");
				creatorDiv += ("  <option value=\"RC\"");
				if (creators[i][j][3] == "RC")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">RC</option>\n");
				creatorDiv += ("  <option value=\"SOC\"");
				if (creators[i][j][3] == "SOC")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">SOC</option>\n");
				creatorDiv += ("  <option value=\"UC\"");
				if (creators[i][j][3] == "UC")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">UC</option>\n");
				creatorDiv += ("  <option value=\"WP\"");
				if (creators[i][j][3] == "WP")
				{
					creatorDiv += (" selected");
				}
				creatorDiv += (">WP</option>\n");
				creatorDiv += ("</select>\n");

			//alert(creators[i][j][4]);
			creatorDiv += ("<h3><label for=\"affiliation\" title=\"name_affiliation_otherAffiliation\">Other Affiliation</label></h3>\n");
			creatorDiv += ("<textarea class=\"text\" type=\"text\" id=\"affiliation\" name=\"affiliation" + i + "-" + j + "-4\" rows=\"3\" cols=\"20\">" + creators[i][j][4] + "</textarea>\n");

			creatorDiv += "</fieldset>\n\n";
		}
		creatorDiv += "</fieldset>\n\n";
	}
	creatorDiv += ("<h3><input class=\"submit\" type=\"button\" onClick=\"addCreator();\" value=\"Add Creator\" /></h3>\n");
	$("#creatorDiv").html(creatorDiv);
}

/*
 * Rebuild the creators array in order to capture changes that may have been
 * made to the input fields.
 */
function getCreatorInfo() {
	creatorNum = -1;
	creators = new Array();
	$("#creatorDiv").find("#creator_name, #creator_orcid, #creator_type, #creator_url, #creator_contactPoint, #affiliation").map(function()
	{
		if (this.id == "creator_name")
		{
			creators[++creatorNum] = new Array();
			creators[creatorNum][0] = new Array();

			// Creator name is stored in creators[creatorNum][0][0]
			creators[creatorNum][0][0] = this.value;
		}
		else if (this.id == "creator_orcid")
		{
			// Creator ORCiD is stored in creators[creatorNum][0][1]
			creators[creatorNum][0][1] = this.value;
		}
		else if (this.id == "creator_type")
		{
			// Creator type is stored in creators[creatorNum][0][2]
			creators[creatorNum][0][2] = this.value;
		}
		else if (this.id == "creator_url")
		{
			// Creator URL is stored in creators[creatorNum][0][3]
			creators[creatorNum][0][3] = this.value;
		}
		else if (this.id == "creator_contactPoint")
		{
			// Creator Contact Point is stored in creators[creatorNum][0][4]
			creators[creatorNum][0][4] = this.value;

			affiliationType = 0;
			affiliationNum = 1;
		}
		else
		{
			if (affiliationType == 0)
			{
				// Create affiliation array
				creators[creatorNum][affiliationNum] = new Array();
			}

			creators[creatorNum][affiliationNum][affiliationType++] = this.value;

			if (affiliationType == 5)
			{
				// Reached end of this affiliation -- set new affiliationType and affiliationNum
				affiliationType = 0;
				affiliationNum++;
			}
		}
	});
}

/*
 * Add an empty array element to the end of the creators array and redisplay.
 */
function addCreator() {
	getCreatorInfo();

	creatorNum = creators.length;
	if (creatorNum == -1) {
		creatorNum = 0;
	}
	creators[creatorNum] = new Array();
	creators[creatorNum][0] = new Array();
	creators[creatorNum][0][0] = "";   // Creator Name
	creators[creatorNum][0][1] = "";   // Creator ORCiD
	creators[creatorNum][0][2] = "";   // Creator Type
	creators[creatorNum][0][3] = "";   // Creator URL
	creators[creatorNum][0][4] = "";   // Creator Contact Point
	creators[creatorNum][1] = new Array();
	creators[creatorNum][1][0] = "";   // MSU College
	creators[creatorNum][1][1] = "";	//MSU College abbr
	creators[creatorNum][1][2] = "";   // MSU Department
	creators[creatorNum][1][3] = "";   // MSU Department abbr
	creators[creatorNum][1][4] = "";   // Other Affiliation

	displayCreators();
}

/*
 * Add affiliation elements to the end of the creators[creatorNum] array and redisplay.
 */
function addAffiliation(creatorNum) {
	getCreatorInfo();

	affiliationNum = creators[creatorNum].length;
	creators[creatorNum][affiliationNum] = new Array();
	creators[creatorNum][affiliationNum][0] = "";
	creators[creatorNum][affiliationNum][1] = "";
	creators[creatorNum][affiliationNum][2] = "";
	creators[creatorNum][affiliationNum][3] = "";
	creators[creatorNum][affiliationNum][4] = "";

	displayCreators();
}

/*
 * Remove the specified creator element from the creators array and redisplay.
 */
function removeCreator(creatorNum) {
	getCreatorInfo();

	creators.splice(creatorNum, 1);

	displayCreators();
}

/*
 * Remove the specified affiliation element from the specified creator array element and redisplay.
 */
function removeAffiliation(creatorNum, affiliationNum) {
	getCreatorInfo();

	creators[creatorNum].splice(affiliationNum, 1);

	displayCreators();
}

